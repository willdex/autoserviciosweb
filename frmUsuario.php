require 'clsusuario.php';
include_once 'conexionusersql.php';

//if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Decodificando formato Json
	switch ($_GET['opcion']) {
		case 'get_perfil':
			   get_perfil();
			break;
		case 'cargar_perfil':
			   cargar_perfil();
			break;
		case 'insertar_casa':
			    insertar_casa();
			break;
		case 'insertar_oficina':
			    insertar_oficina();
			break;
		case 'insertar_trabajo':
			    insertar_trabajo();
			break;		
		case 'get_usuario_por_empresa':
		    get_usuario_por_empresa();
		    break;
		case 'get_usuario_sin_empresa':
		    get_usuario_sin_empresa();
		    break;
		case 'insertar_usuario_a_empresa':
		    insertar_usuario_a_empresa();
		    break;


		case 'pedir_moto':
		    pedir_moto();
		    break;
		case 'insertar_imagen':
		    insertar_imagen();
		    break;
		case 'existe_telefono':
		   existe_telefono();
		   break;
		case 'insertar_usuario':
		    insertar_usuario();
		   break;
		case 'verificar_tipo_de_usuario':
			verificar_tipo_de_usuario();
		   break;
		case 'insertar_usuario_por_administrador':
			insertar_usuario_por_administrador();
		 break;
		 case 'modificar_nombre_apellido':
			modificar_nombre_apellido();
		 break;
		 case 'set_estado_inactivo_usuario':
			set_estado_inactivo_usuario();
		 break;
		 
		default:
			
			break;
	}

function verificar_tipo_de_usuario()
{
$dato=json_decode(file_get_contents("php://input"),true);
	$sw_moto=Usuario::existe_telefono_moto($dato['celular']);
	$sw_usuario=Usuario::existe_telefono($dato['celular']);
	if($sw_usuario===true)
	{
		enviar_solicitud_de_codigo($dato['celular']);
		print json_encode( array('suceso' => '1','mensaje' => 'Cuenta de usuario.','tipo'=>'usuario' ));
	}
	else if($sw_moto===true)
	{   
		print json_encode( array('suceso' => '1','mensaje' => 'Cuenta de moto.','tipo'=>'moto'));
	}else
	{
		print json_encode( array('suceso' => '2','mensaje' => 'No tiene ningun registro.'));
	}	
}


function existe_telefono()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$sw_moto=Usuario::existe_telefono_moto($dato['celular']);
	$sw_usuario=Usuario::existe_telefono($dato['celular']);
	if($sw_usuario===true)
	{
		print json_encode( array('suceso' => '1','mensaje' => 'Ustdes ya tiene una cuenta registrada.'));
	}
	else if($sw_moto===true)
	{   
		print json_encode( array('suceso' => '1','mensaje' => 'Ustdes ya tiene una cuenta registrada como Motista.'));
	}else
	{
		print json_encode( array('suceso' => '2','mensaje' => 'Aun no tiene ningun registro.'));
	}
}

 function cargar_perfil()
{
	
	$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::set_perfil($dato['nombre'],$dato['apellido'],$dato['telefono'],$dato['email'],$dato['token']);
	// Recibimos el retorno y preguntamos si es 
	if ($retorno===true){	
			$retorno = Usuario::get_perfil($dato['telefono']);
				if ($retorno) {
							print json_encode(array('suceso'=>'1','mensaje'=>'Cargado correctamente.','id'=>$retorno['id'],'nombre'=>$retorno['nombre'],'apellido'=>$retorno['apellido'],'telefono'=>$retorno['telefono'],'email'=>$retorno['email'],'id_empresa'=>$retorno['id_empresa']));
						} else {
							//Enviar respuesta de error general
							print json_encode(
								 array(
								 	'suceso' => '2',
								 	'mensaje' => 'Error al Cargar los datos.'
								 	));
						}
		}
	else if(Usuario::existe_telefono($dato['telefono'])===true)
	{
		$retorno=Usuario::update_usuario($dato['nombre'],$dato['apellido'],$dato['telefono'],$dato['email'],$dato['token']);
	// Recibimos el retorno y preguntamos si es 
		if ($retorno===true){	

			$retorno = Usuario::get_perfil($dato['telefono']);
				if ($retorno) {
							print json_encode(array('suceso'=>'1','mensaje'=>'Cargado correctamente.','id'=>$retorno['id'],'nombre'=>$retorno['nombre'],'apellido'=>$retorno['apellido'],'telefono'=>$retorno['telefono'],'email'=>$retorno['email'],'id_empresa'=>$retorno['id_empresa']));
						} else {
							//Enviar respuesta de error general
							print json_encode(
								 array(
								 	'suceso' => '2',
								 	'mensaje' => 'Error al Cargar los datos.'
								 	));
						}
			}
				else
 	{
		$respuesta["suceso"] = "2";
        	$respuesta["mensaje"] = "Error al cargar!";
       		print json_encode($respuesta);  	
	} 
	}
	else
 	{
		$respuesta["suceso"] = "2";
        	$respuesta["mensaje"] = "Error al cargar!";
       		print json_encode($respuesta);  	
	} 
}
 function get_perfil()
{
	$cuerpo = json_decode(file_get_contents("php://input"),true);

				$retorno = Usuario::get_perfil($cuerpo['telefono']);
		 		$token=Usuario::cargar_token($cuerpo['telefono'],$cuerpo['token']);
		 		$configuracion=Usuario::get_configuracion();


					$evaluar = verificar_codigo($cuerpo['telefono'],$cuerpo['codigo']);
		 		
			 		$validar = json_decode($evaluar,true);
			 		
			 		if ($validar['success']==true) 
			 		{

					  if ($retorno!="-1" && $token===true) {


											
								//Envia objeto json del usuario

	 								$dato["suceso"]= "1";
									$dato["mensaje"]= "Correcto.";
									$dato['id']=$retorno['id'];
									$dato['nombre']=$retorno['nombre'];
									$dato['apellido']=$retorno['apellido'];
									$dato['telefono']=$retorno['telefono'];
									$dato['email']=$retorno['email'];
									$dato['id_empresa']=$retorno['id_empresa'];	
									$dato['configuracion']=$configuracion;	
									
									$empresa=Usuario::get_empresa($retorno['id']);
									if($empresa!=-1)
									{
										$dato['nombre_empresa']=$empresa['nombre'];	
									}
									else
									{
										$dato['nombre_empresa']="";
									}

									$row=Usuario::get_administrador($retorno['id']);
									if($row!="-1")
										 {
										  	 $dato["empresa"]=$row;
											
										 }
										 else
										 {
											$dato["empresa"]="";
										 }	
										 print json_encode($dato);


							} else {
								//Enviar respuesta de error general
								print json_encode(
									 array(
									 	'suceso' => '2',
									 	'mensaje' => 'No se obtuvo el perfil'
									 	));
							}
						}
				else
				{
					print json_encode(
							 array(
							 	'suceso' => '2',
							 	'mensaje' => 'El codigo no es valido'
							 	));
				}


}


function insertar_casa()
{
	$dato=json_decode(file_get_contents("php://input"),true);
    $retorno=Usuario::insertar_casa($dato['detalle'],$dato['latitud'],$dato['longitud'],$dato['id_usuario']);
    if($retorno==true)
    {
    	print json_encode(array('suceso' => '1','mensaje' => 'Se inserto correctamente.'));
    }
    else
    {
    	print json_encode(array('suceso' => '2','mensaje' => 'No se obtuvo el perfil'));
    }
}
function insertar_oficina()
{
	$dato=json_decode(file_get_contents("php://input"));
    $retorno=Usuario::insertar_oficina($dato['detalle'],$dato['latitud'],$dato['longitud'],$dato['id_usuario']);
    if($retorno==true)
    {
    	print json_encode(array('suceso' => '1','mensaje' => 'Se inserto correctamente.'));
    }
    else
    {
    	print json_encode(array('suceso' => '2','mensaje' => 'No se obtuvo el perfil'));
    }
}
function insertar_trabajo()
{
	$dato=json_decode(file_get_contents("php://input"),true);
    $retorno=Usuario::insertar_trabajo($dato['detalle'],$dato['latitud'],$dato['longitud'],$dato['id_usuario']);
    if($retorno==true)
    {
    	print json_encode(array('suceso' => '1','mensaje' => 'Se inserto correctamente.'));
    }
    else
    {
    	print json_encode(array('suceso' => '2','mensaje' => 'No se obtuvo el perfil'));
    }
}

function get_direcciones_establecidas()
{
	$dato=json_decode(file_get_contents("php://input"),true);
    $casa=Usuario::get_casa($dato['id_usuario']);
    $oficina=Usuario::get_oficina($dato['id_usuario']);
    $trabajo=Usuario::get_trabajo($dato['id_usuario']);
    if($casa=="-1" && $oficina=="-1" && $trabajo=="-1")
    {
    	print json_encode(array('suceso' => '2','mensaje' => 'No se obtuvo las direccion'));
    }
    else
    {
    	$dato['suceso']="1";
    	$dato['mensaje']='Se inserto correctamente.';
    	$dato['casa']=array($casa);
    	$dato['oficina']=array($oficina);
    	$dato['trabajo']=array($trabajo);
    	print json_encode(array($dato));
    }
}


function get_usuario_por_empresa()
{
	$dato=json_decode(file_get_contents("php://input"),true);
    $contacto=Usuario::get_usuario_por_empresa($dato['id_empresa']);
    if($contacto=="-1")
    {
    	print json_encode(array('suceso' => '2','mensaje' => 'No tiene usuarios registrados.'));
    }
    else
    {
    	$dato['suceso']="1";
    	$dato['mensaje']='Lista completada';
    	$dato['contacto']=$contacto;
    	print json_encode($dato);
    }
}

function get_usuario_sin_empresa()
{
	$dato=json_decode(file_get_contents("php://input"),true);
    $contacto=Usuario::get_usuario_sin_empresa();
    if($contacto=="-1")
    {
    	print json_encode(array('suceso' => '2','mensaje' => 'No tiene usuarios registrados.'));
    }
    else
    {
    	$dato['suceso']="1";
    	$dato['mensaje']='Lista completada.';
    	$dato['contacto']=$contacto;
    	print json_encode($dato);
    }
}

function pedir_moto()
{
	$dato=json_decode(file_get_contents("php://input"),true);
    $res=Usuario::pedir_moto($dato['id_usuario'],$dato['latitud'],$dato['longitud']);
    if($res===true)
    {
    	print json_encode(array('suceso' => '1','mensaje' => 'Pedir enviado correctamente.'));
    }
    else
    {
    	print json_encode(array('suceso' => '2','mensaje' => 'No tiene usuarios registrados.'));
    }
}
function insertar_imagen()
{
	$dato=json_decode(file_get_contents("php://input"),true);
   
	 	 $image = $dato['imagen'];
		 $id=$dato['id_usuario'];
	 	 $con = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE) or die('error al conectarse');
		 $sql = "UPDATE usuario set imagen=? where id= ?";
	 
		 $stmt = mysqli_prepare($con,$sql);
	 
		 mysqli_stmt_bind_param($stmt,"ss",$image,$id);
		 mysqli_stmt_execute($stmt);
		 
		 $check = mysqli_stmt_affected_rows($stmt);
	 
		 if($check == 1)
		 {
		 print json_encode(array('suceso' => '1','mensaje' => 'Se ha subido la imagen con exito.'));
		 }
		 else
		 {
		 	print json_encode(array('suceso' => '2','mensaje' => 'Tuvimos problemas al subir la imagen'));
		 }
		 mysqli_close($con);

}

function insertar_usuario()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::insertar_usuario($dato['nombre'],$dato['apellido'],$dato['telefono'],$dato['email'],$dato['token'],$dato['imagen']);
	if($retorno!="-1")
	{
		$dato['suceso']="1";
    	$dato['mensaje']="Correcto.";
    	$dato['id_usuario']=$retorno;
    	print json_encode($dato);
		 }
		 else
		 {
		 	print json_encode(array('suceso' => '2','mensaje' => 'Tuvimos problemas al subir la imagen'));
		 }
}
function insertar_usuario_por_administrador()
{
	$dato=json_decode(file_get_contents("php://input"),true);
$celular=$dato['telefono'];
$nombre=$dato['nombre'];
$id_empresa=$dato['id_empresa'];

$id_usuario=Usuario::existe_usuario_por_telefono($celular);

if($id_usuario!=-1)
{
	$existe=Usuario::obtener_id_empresa_por_id_usuario($id_usuario);

	if($existe==$id_empresa)
	{
		$actualizar=Usuario::set_estado_activo_usuario($id_usuario);
			
		if($actualizar==true)
		{
		print json_encode(array('suceso' => '1','mensaje' => 'Se habilito a '.$nombre.' para hacer pedidos.','id_usuario'=>$id_usuario));
		}
		else
		{
		print json_encode(array('suceso' => '2','mensaje' => 'Vuelve a intentarlo.'));	
		}
	}
	else if($existe!=-1)
	{
		$registrar=Usuario::insertar_usuario_a_empresa($id_usuario,$id_empresa);
		if($registrar==true)
		{
    	 print json_encode(array('suceso' => '1','mensaje' => 'Se registro correctamente.','id_usuario'=>$id_usuario));
		 }
		 else
		 {
		 	print json_encode(array('suceso' => '2','mensaje' => 'Vuelva a intentar nuevamente.'));
		 }

	}
	else
	{
		print json_encode(array('suceso' => '2','mensaje' => 'Este numero ya esta registrado en otra empresa'));
	}
}
else
{

		$registrar=Usuario::insertar_usuario_por_administrador($nombre,$celular,$id_empresa);
		if($registrar!=-1)
		{
    	 print json_encode(array('suceso' => '1','mensaje' => 'Se registro correctamente.','id_usuario'=>$id_usuario));
		 }
		 else
		 {
		 	print json_encode(array('suceso' => '2','mensaje' => 'Vuelva a intentar nuevamente.'));
		 }
}
	

		
}
function insertar_usuario_a_empresa()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::insertar_usuario_a_empresa($dato['id_usuario'],$dato['id_empresa']);
	if($retorno===true)
	{
		$dato['suceso']="1";
    	$dato['mensaje']="Agregado correctamente.";    	
    	print json_encode($dato);
		 }
		 else
		 {
		 	print json_encode(array('suceso' => '2','mensaje' => 'Tuvimos problemas al agregar el usuario.'));
		 }
}

function modificar_nombre_apellido()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::modificar_nombre_apellido($dato['id_usuario'],$dato['nombre'],$dato['apellido']);
	if($retorno===true)
	{
		$dato['suceso']="1";
    	$dato['mensaje']="Se modifico correctamente.";    	
    	print json_encode($dato);
		 }
		 else
		 {
		 	print json_encode(array('suceso' => '2','mensaje' => 'Vuelva a intentarlo.'));
		 }
}

function set_estado_inactivo_usuario()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::set_estado_inactivo_usuario($dato['id_usuario']);
	if($retorno===true)
	{
		$dato['suceso']="1";
    	$dato['mensaje']="Se modifico correctamente.";    	
    	print json_encode($dato);
		 }
		 else
		 {
		 	print json_encode(array('suceso' => '2','mensaje' => 'Vuelva a intentarlo.'));
		 }
}

function enviar_solicitud_de_codigo($criterio)
{
	$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.authy.com/protected/json/phones/verification/start?via=sms&country_code=591&phone_number='".$criterio."'&locale=es",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_HTTPHEADER => array(
				    "cache-control: no-cache",
				    "x-authy-api-key: RW575BqnHAdQFKNRsz1NC5p4R0yGRYtd"
				  ),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) 
				{
				  echo "cURL Error #:" . $err;
				} 
				else 
				{
				  //echo $response;
				}
}


function verificar_codigo($nro,$cod)
{
				$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.authy.com/protected/json/phones/verification/check?country_code=591&phone_number='".$nro."'&verification_code=".$cod,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "x-authy-api-key: RW575BqnHAdQFKNRsz1NC5p4R0yGRYtd"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {

  return $response;
}
}

 ?>