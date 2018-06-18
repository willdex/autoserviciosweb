<?php 
/**
* Loguear al usuario 
*/
require 'usuariotur.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Decodificando formato Json
	$cuerpo = json_decode(file_get_contents("php://input"),true);

	// Loguear Usuario
	$retorno = usuarios::login(
		$cuerpo['Nro'],
		$cuerpo['password']);

	// Recibimos el retorno y preguntamos si es 
	//false o true
	if ($retorno === true) {
		$respuesta["suceso"] = "1";
        		$respuesta["mensaje"] = "Login correcto!";
       		 print json_encode($respuesta);
	} else{
		$respuesta["suceso"] = "2";
		$respuesta["mensaje"] = "incorrento el nro o contraseña";
		print json_encode($respuesta);
	}
}

 ?>