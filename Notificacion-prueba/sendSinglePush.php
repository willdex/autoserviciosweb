<?php
	//enviar notificaciones a un solo dispositivo
		//probar de Postman con los parametros 
	//importing required files 
require_once 'DbOperation.php';
require_once 'Firebase.php';
require_once 'Push.php'; 
 
$db = new DbOperation();
 
$response = array(); 

			$push = new Push(['title'],['message'],null);
			
 
			 $mPushNotification = $push->getPush(); 
			 
			 
			 $devicetoken = $db->getTokenByEmail($_POST['email']);
			 
			 
			 $firebase = new Firebase(); 
			 

			 echo $firebase->send($devicetoken, $mPushNotification);
	 	
 
	echo json_encode($response);


?>