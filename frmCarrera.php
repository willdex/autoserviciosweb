<?php
require('clscarrera.php');



switch ($_GET['opcion']) {
		case 'iniciar_carrera':
			   iniciar_carrera();
			break;
		case 'finalizar_carrera':
			   set_moto();
			break;
		case 'get_carrera_en_curso':
		   get_carrera_en_curso();
			break;
		case 'terminar_carrera':
			terminar_carrera();
			break;
		case 'terminar_carrera_pedido':
			terminar_carrera_pedido();
			break;
		case 'lista_de_carrera_por_usuario':
			lista_de_carrera_por_usuario();
			break;
		case 'lista_de_carrera_por_moto':
			lista_de_carrera_por_moto();
			break;
		case 'existe_carrera_por_id_pedido':
		     existe_carrera_por_id_pedido();
		     break;

		case 'lista_de_carreras_por_id_pedido':
		 	 lista_de_carreras_por_id_pedido();
		 	 break;  
		case 'continuar_con_nueva_carrera':
		     continuar_con_nueva_carrera();
		     break; 
		case 'rutas_por_id_usuario':
		  	rutas_por_id_usuario();
		  	break;  
		default:			
			break;
	}
function iniciar_carrera()
{
$dato=json_decode(file_get_contents("php://input"),true);
$dato=Carrera::iniciar_carrera($dato['nombre'],$dato['apellido'],$dato['ci'],$dato['celular'],$dato['email'],$dato['marca'],$dato['modelo'],$dato['placa'],$dato['direccion'],$dato['telefono'],$dato['referencia'],$dato['codigo'],$dato['credito']);
if($dato)
{print json_encode(array('suceso' =>'1','mensaje'=>'Se cargo correctamente.' ));}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'Ocurrio un problema al cargar.' ));}
}
#no recuerdo donde esta esta function,,,,,,,......
function finalizar_carrera()
{
	$dato=json_decode(file_get_contents("php://input"),true);
$row=Carrera::finalizar_carrera($dato['celular'],$dato['codigo']);
if($row)
{
	print json_encode(array('suceso'=>'1','nombre'=>$row['nombre'],'apellido'=>$row['apellido'],'ci'=>$row['ci'],'celular'=>$row['celular'],'email'=>$row['email'],'marca'=>$row['marca'],'modelo'=>$row['modelo'],'placa'=>$row['placa'],'direccion'=>$row['direccion'],'telefono'=>$row['telefono'],'referencia'=>$row['referencia'],'codigo'=>$row['codigo'],'credito'=>$row['credito']));
}
else
{
	print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error al ingresar los datos.' ));
}
}
#termino la carrera,,..............
function terminar_carrera()
{
	$dato=json_decode(file_get_contents("php://input"),true);

	$id_direccion=Carrera::insertar_direccion($dato['latitud'],$dato['longitud'],$dato['detalle'],$dato['id_usuario']);

	$distancia=Carrera::get_distancia($dato['id_carrera'],$dato['latitud'],$dato['longitud']);
 	$row=Carrera::terminar_carrera($id_direccion,$distancia,$dato['opciones'],$dato['id_pedido'],$dato['id_usuario'],$dato['id_moto'],$dato['id_carrera']);

 		$car=Carrera::iniciar_carrera($id_direccion,"0",$dato['id_pedido'],$dato['id_usuario'],$dato['id_moto']);

 	if($row===true && $car!="-1"){
 			print json_encode(array('suceso' =>'1' ,'mensaje'=>'Carrera finalizada y iniciada con exito.','id_carrera'=>$car));
 	}
	 else
	 {
 	print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error al terminar la carrera.' ));
	 }
}

#termino la carrera,,..............
function terminar_carrera_pedido()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$id_direccion=Carrera::insertar_direccion($dato['latitud'],$dato['longitud'],$dato['detalle'],$dato['id_usuario']);
	$distancia=Carrera::get_distancia($dato['id_carrera'],$dato['latitud'],$dato['longitud']);
 	$row=Carrera::terminar_carrera_pedido($id_direccion,$distancia,$dato['opciones'],$dato['id_pedido'],$dato['id_usuario'],$dato['id_moto'],$dato['id_carrera']);
	
	$credito=Carrera::get_credito_por_id_moto($dato['id_moto']);
 	if($row===true){
 	print json_encode(array('suceso' =>'1' ,'mensaje'=>'Se finalizo correctamente.','credito'=>$credito));
 	}
	 else
	 {
 	print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error al finaliza.' ,'credito'=>$credito));
	 }
}

function continuar_con_nueva_carrera()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$id_direccion=Carrera::insertar_direccion($dato['latitud'],$dato['longitud'],$dato['detalle'],$dato['id_usuario']);

 		$car=Carrera::iniciar_carrera($id_direccion,"0",$dato['id_pedido'],$dato['id_usuario'],$dato['id_moto']);

 	if($car=="-2")
 	{
 	print json_encode(array('suceso' =>'1' ,'mensaje'=>'El pedido a sido cancelado.'));
 	}
 		else if($car!="-1")
 	{
 	print json_encode(array('suceso' =>'1' ,'mensaje'=>'Se ha iniciado la carrera con Exito' ,'id_carrera'=>$car));
 	}
	 else
	 {
 	print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error al iniciar la carrera.' ));
	 }
}

function get_carrera_en_curso()
{
$dato=json_decode(file_get_contents("php://input"),true);
$row=Carrera::get_carrera_en_curso($dato['id_pedido']);
if($row!="-1")
{ $direccion_inicio=Carrera::get_direccion_por_id($row['direccion_inicio']);
  $direccion_fin=Carrera::get_direccion_por_id($row['direccion_fin']);
  if($direccion_fin=="-1")
  {
 	$direccion_fin="";
  }
  $dato['suceso']="1";
  $dato['mensaje']="Correcto.";
  $dato['carrera']= array($row);
  $dato['inicio']= array($direccion_inicio);
  $dato['fin']= array($direccion_fin);

  print json_encode($dato);	
}
else
{
	print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error al ingresar los datos.' ));
}

}

function lista_de_carrera_por_usuario()
{
$dato=json_decode(file_get_contents("php://input"),true);
$row=Carrera::lista_de_carrera_por_usuario($dato['id_usuario'],$dato['id_pedido']);
if($row!="-1")
{
  $dato['suceso']="1";
  $dato['mensaje']="Correcto.";
  $dato['carrera']= $row;
  print json_encode($dato);	
}
else
{
	print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error al ingresar los datos.' ));
}
}

function lista_de_carrera_por_moto()
{
$dato=json_decode(file_get_contents("php://input"),true);
$row=Carrera::lista_de_carrera_por_moto($dato['id_moto'],$dato['id_pedido']);
if($row!="-1")
{
  $dato['suceso']="1";
  $dato['mensaje']="Correcto.";
  $dato['carrera']= $row;
 
  
  print json_encode($dato);	
}
else
{
	print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error al ingresar los datos.' ));
}
}

function  existe_carrera_por_id_pedido()
{
$dato=json_decode(file_get_contents("php://input"),true);
$row=Carrera::existe_carrera_por_id_pedido($dato['id_pedido']);
	if($row===true)
	{
 	print json_encode(array('suceso' =>'1' ,'mensaje'=>'Tiene Carreras.' ));
 	}
	 else
	 {
 	print json_encode(array('suceso' =>'2' ,'mensaje'=>'No tiene ninguna carrera registrado.' ));
	 }
}

function lista_de_carreras_por_id_pedido()
{
$dato=json_decode(file_get_contents("php://input"),true);
$row=Carrera::lista_de_carreras_por_id_pedido($dato['id_pedido']);
if($row!="-1")
{
  $dato['suceso']="1";
  $dato['mensaje']="Correcto.";
  $dato['carrera']= $row;
  print json_encode($dato);	
}
else
{
	print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error al obtener los puntos del pedido' ));
}
}

function rutas_por_id_usuario()
{
  $dato=json_decode(file_get_contents("php://input"),true);
$row=Carrera::rutas_por_id_usuario($dato['id_usuario']);
if($row!="-1")
{
  $dato['suceso']="1";
  $dato['mensaje']="Correcto.";
  $dato['ruta']= $row;
  print json_encode($dato);	
}
else
{
	print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error al obtener los puntos del pedido' ));
}
}





?>