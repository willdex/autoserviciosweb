<?php


require_once 'DbOperation.php';
 $response = array(); 
 
 if($_SERVER['REQUEST_METHOD']=='POST'){
 
 $token = $_POST['token'];
 $email = $_POST['email'];
 
 $db = new DbOperation(); 
 
 $result = $db->registerDevice($email,$token);
 
 if($result == 0)
 {
 	$response['error'] = false; 
 	$response['message'] = 'Dispositivo Registrado Exitosamente';
 }
	 elseif($result == 2)
	 {
	 	$response['error'] = true; 
	 	$response['message'] = 'Este Dispositivo ya ha Sido Registrado Anteriormente';
	 }
 else
 	{
 		$response['error'] = true;
 		$response['message']='Dispositivo NO Registrado ERROR';
 	}
 }
 else
 {
 	$response['error']=true;
 	$response['message']='Solicitud NO VALIDA...';
 }
 
 echo json_encode($response);

?>