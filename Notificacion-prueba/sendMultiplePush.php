<?php

	
require_once 'DbOperation.php';
require_once 'Firebase.php';
require_once 'Push.php'; 
 
$db = new DbOperation();
 
$response = array(); 

	
 	$push = new Push(['title'],['message'],null);

		// obteniendo el empuje del objeto push
		 $mPushNotification = $push->getPush(); 
		 
		 // obtener el token del objeto de base de datos
		 $devicetoken = $db->getAllTokens();
		 

		// creación de objeto de clase firebase
		 $firebase = new Firebase(); 
		 
		 // envío de notificación push y visualización de resultados
		 echo $firebase->send($devicetoken, $mPushNotification);

 
//echo json_encode($response);

?>

