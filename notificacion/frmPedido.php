<?php
require ('clspedido.php');

switch ($_GET['opcion']) {
	case 'get_pedidos':
		get_pedidos();
		break;
	case 'pedido_en_curso':
		pedido_en_curso();
		break;
	case 'get_pedido_por_celular_usuario':
	    get_pedido_por_celular_usuario();
	    break;
	case 'llego_la_moto':
		llego_la_moto();
	break;
	case 'lista_pedido_por_id_usuario':
		lista_pedido_por_id_usuario();
	break;

	case 'lista_pedido_por_id_usuario_por_fecha':
		lista_pedido_por_id_usuario_por_fecha();
	break;
	case 'lista_pedido_por_id_moto_por_fecha':
		lista_pedido_por_id_moto_por_fecha();
	break;
    case 'lista_pedido_por_id_empresa_por_fecha':
		lista_pedido_por_id_empresa_por_fecha();
	break;
	case 'get_estado_pedido':
		get_estado_pedido();
		break;
	case 'terminar_todo_pedido':
	     terminar_todo_pedido();
	     break; 
	case 'pedir_moto':
	 	pedir_moto();
	 	break;
	case 'aceptar_pedido':
	 	aceptar_pedido();
	     break;
	case 'get_pedido_por_id_pedido':
		get_pedido_por_id_pedido();
		break;
	case 'get_pedido_por_id_moto':
		get_pedido_por_id_moto();
		break;
	case 'estoy_cerca':
		estoy_cerca();
		break;
		//solo cuando no hay intetnet...7
	case 'get_id_ultimo_pedido_por_id_usuario':
		get_id_ultimo_pedido_por_id_usuario();
		break;
	case 'get_pedido_por_id_usuario':
		get_pedido_por_id_usuario();
	break;	
	case 'cancelar_pedido':
		cancelar_pedido();
	break;	
	case 'estoy_cerca':
		estoy_cerca();
	break;	
	case 'notificacion_llego_la_moto':
		notificacion_llego_la_moto();
	break;	

	case 'verificar_si_el_pedido_se_acepto':
		verificar_si_el_pedido_se_acepto();
	break;	
	case 'set_puntuacion':
		set_puntuacion();
	break;	
	default:
		
		break;
}

function get_pedidos()
{
$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_pedidos_por_id_motista($dato['id_moto']);
	if($row)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["pedido"]=$row;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Error al obtener los datos del servidor."  ));
	}
		
}
function get_ped()
{$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_pedidos_por_id_motista($dato['id_moto']);
	if($row)
	{
		foreach ($row as $registro) {
		$dato[]=array("suceso"=>"1","id"=>$registro["id"],"id_usuario"=>$registro["id_usuario"],"id_moto"=>$registro["id_moto"],"calificacion"=>$registro["calificacion"],"tipo_pedido"=>$registro["tipo_pedido"],"mensaje"=>$registro["mensaje"],"fecha"=>$registro["fecha"],"fecha_llegado"=>$registro["fecha_llegado"],"estado"=>$registro["estado"],"latitud"=>$registro["latitud"],"longitud"=>$registro["longitud"],"nombre_usuario"=>$registro["nombre_usuario"]);
		}
	}
	else
	{ 
		$dato[]= array("suceso"=>"2","mensaje"=>"Error al obtener los datos del servidor."  );
	}
	print json_encode($dato);
}
function get_pedido_por_celular_usuario()
{

  $dato=json_decode(file_get_contents("php://input"),true);
  $row=Pedido::get_pedido_por_celular_usuario($dato['celular']);
  if($row)
  {
  	$resultado['suceso']="1";
    $resultado['mensaje']="Correcto.";
    $resultado['pedido']= array($row);


   print json_encode($resultado);

  }else
  {
   print json_encode(array("suceso"=>"2","mensaje"=>"No tiene pedidos habilitados."  ));
  }
}

function get_pedido_por_id_pedido()
{

  $dato=json_decode(file_get_contents("php://input"),true);
  $row=Pedido::get_pedido_por_id_pedido($dato['id_pedido']);
  if($row!="-1")
  {
  	$resultado['suceso']="1";
    $resultado['mensaje']="Correcto.";
    $resultado['pedido']= array($row);
   print json_encode($resultado);

  }else
  {
   print json_encode(array("suceso"=>"2","mensaje"=>"No tiene pedidos habilitados."  ));
  }
}
function get_pedido_por_id_usuario()
{

  $dato=json_decode(file_get_contents("php://input"),true);
  $row=Pedido::get_pedido_por_id_usuario($dato['id_usuario']);
  if($row!="-1")
  {
  	$resultado['suceso']="1";
    $resultado['mensaje']="Correcto.";
    $resultado['pedido']= array($row);
   print json_encode($resultado);

  }else
  {
   print json_encode(array("suceso"=>"2","mensaje"=>"No tiene pedidos habilitados."  ));
  }
}

function get_pedido_por_id_moto()
{

  $dato=json_decode(file_get_contents("php://input"),true);
  $row=Pedido::get_pedido_por_id_moto($dato['id_moto']);
  if($row!="-1")
  {
  	$resultado['suceso']="1";
    $resultado['mensaje']="Correcto.";
    $resultado['pedido']= array($row);
    $car=Pedido::get_carrera_en_curso_por_id($row['id']);
    if($car!='-1')
    {$resultado['carrera']= array($car);}
    else
    {$resultado['carrera']= "";}

   print json_encode($resultado);

  }else
  {
   print json_encode(array("suceso"=>"2","mensaje"=>"No tiene pedidos habilitados."  ));
  }
}


function llego_la_moto()
{ $dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::llego_la_moto($dato['id_pedido']);
	if($row===true)
	{
		print json_encode(array('suceso' =>'1' ,'mensaje'=>'Pedido finalizado correctamente.'));
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error: al finalizar el pedido.' ));
	}
}
function lista_pedido_por_id_usuario()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::lista_pedido_por_id_usuario($dato["id_usuario"]);
	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["historial"]=$row;
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error: al finalizar el pedido.' ));
	}
}
function lista_pedido_por_id_usuario_por_fecha()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::lista_pedido_por_id_usuario_por_fecha($dato["id_usuario"],$dato["dia"],$dato["mes"],$dato["anio"]);
	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["historial"]=$row;
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No pudimos obtener el historial.' ));
	}
}

function lista_pedido_por_id_moto_por_fecha()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::lista_pedido_por_id_moto_por_fecha($dato["id_moto"],$dato["dia"],$dato["mes"],$dato["anio"]);
	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["historial"]=$row;
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No pudimos obtener el historial.' ));
	}
}

function lista_pedido_por_id_empresa_por_fecha()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::lista_pedido_por_id_empresa_por_fecha($dato["id_empresa"],$dato["dia"],$dato["mes"],$dato["anio"]);
	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["historial"]=$row;
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No pudimos obtener el historial.' ));
	}
}


function pedido_en_curso()
{
$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::pedido_en_curso($dato["id_moto"]);

	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["pedido"]= array($row);
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error: al obtener el ultimo pedido.' ));
	}	
}

function get_estado_pedido()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_estado_pedido($dato["id_pedido"]);

	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["estado"]= $row['estado'];
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error: al obtener el ultimo pedido.' ));
	}	
}
function terminar_todo_pedido()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::terminar_todo_pedido($dato["id_pedido"]);

	if ($row===true) {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error: al finalizar el pedido.' ));
	}	
}
function pedir_moto()
{$sw=false;
	$sw_pedido=false;
	$dato=json_decode(file_get_contents("php://input"),true);

  $id_usuario=$dato['id_usuario'];
  $latitud=$dato['latitud'];
  $longitud=$dato['longitud'];
  $nombre=$dato['nombre'];
  $id_empresa=$dato['id_empresa'];


if(Pedido::id_ultimo_pedido_en_proceso($id_usuario)!=-1)
{
$sw_pedido=true;
}
	if(Pedido::id_ultimo_pedido($id_usuario)!=-1 && $sw_pedido==true)
	{
		print json_encode(array('suceso' => '3','mensaje' => 'ya tiene un pedido en camino.'));
		$sw=true;}

if($sw==false)
{
	
	    $disponible=Pedido::moto_disponible($latitud,$longitud);
		
		if($disponible==true)
		{  
			$estado=Pedido::get_estado_usuario($id_usuario);
			if($estado==1)
			{
				if(Pedido::get_estado_empresa_por_id_usuario($id_usuario)=="1")
				{
				  $p=Pedido::pedir_moto($id_usuario,$latitud,$longitud,"Por favor una moto.",$nombre,$id_empresa);
					if($p!="-1")
					{
					    	print json_encode(array('suceso' => '1','mensaje' => 'Su pedido se ha realizado correctamente.'));
					 }
					else
					{
							print json_encode(array('suceso' => '2','mensaje' => 'Su pedido no a Podido realizarse.'));
				  }
				}
				else
				{
					print json_encode(array('suceso' => '2','mensaje' => 'Su pedido no a Podido realizarse. Por favor contactese con el Administrador de su Empresa.'));
				}
		    }
		    else
		    {
		    	print json_encode(array('suceso' => '2','mensaje' => 'Su cuenta esta inactiva. Por favor contactese con su administrador.'));
		    }

		}
		else
		{
			print json_encode(array('suceso' => '2','mensaje' => 'No hay Motos disponibles.'));
		}

	}
	else
	{
		print json_encode(array('suceso' => '2','mensaje' => 'Su pedido no a Podido realizarse.'));
	}
}

function aceptar_pedido()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Pedido::aceptar_pedido($dato['id_pedido'],$dato['id_moto']);
	if($p===true)
	{  $pedido=Pedido::pedido_en_curso_por_id($dato['id_pedido']);
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["pedido"]=$pedido;
		print json_encode($dato);
	    	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'El pedido ya a sido registrado por otro Motista.'));
	}
}

function estoy_cerca()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Pedido::estoy_cerca($dato['id_pedido']);
	if($p===true)
	{
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto N";
		print json_encode($dato);
	    	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Error al enviar la notificacion.'));
	}
}

function notificacion_llego_la_moto()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Pedido::notificacion_llego_la_moto($dato['id_pedido']);
	if($p===true)
	{
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		print json_encode($dato);
	    	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Error al enviar la notificacion.'));
	}
}



function get_id_ultimo_pedido_por_id_usuario()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Pedido::get_id_ultimo_pedido_por_id_usuario($dato['id_usuario']);
	if($p!=-1)
	{
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["id_pedido"]=$p;
		print json_encode($dato);
	    	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Error al obtener su pedido'));
	}
}

function cancelar_pedido()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::cancelar_pedido($dato["id_pedido"],$dato['distancia']);

	if ($row===true) {
		$dato["suceso"]="1";
		$dato["mensaje"]="Se cancelo con exito el Pedido.";
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Tenemos problemas al cancelar el Pedido.' ));
	}	
}

function set_puntuacion()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::set_puntuacion($dato["id_pedido"],$dato['puntuacion']);

	if ($row===true) {
		$dato["suceso"]="1";
		$dato["mensaje"]="Se modifico la calificacion.";
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Tenemos problemas modifiacion la calificacion.' ));
	}	
}
function verificar_si_el_pedido_se_acepto()
{//funcion para verificar si el pedido fue aceptado por un motista o no. en caso de que no halla aceptado se le enviara una notificacion al usuario, con un mensaje de que no Hay moto disponible... 
	$dato=json_decode(file_get_contents("phhp://input"),true);
	$id_pedido=Pedido::pedido_no_aceptado($dato['id_usuario']);
	if($id_pedido!=-1)
	{
			notificacion_no_hay_moto_disponible($id_pedido);
	}


}

?>