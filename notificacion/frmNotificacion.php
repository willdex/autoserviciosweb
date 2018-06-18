<?php
include_once 'Push.php';
include_once 'Firebase.php';


switch (isset($_POST['btn'])) {
    case 'enviar':
        notificacion();
    break;
}

//define('FIREBASE_API_KEY', 'AIzaSyBTMUxbM3EGbb5ErJZqHjeKmd5avXox4Nc');

function notificacion(){

$push = new Push('ASAPP','MENSAJE DE ASAPP',null,"usuario","","","","","20");
  $mPushNotification = $push->getPush(); 
  echo var_dump($mPushNotification);
    // obtener el token del objeto de base de datos
    //$devicetoken=["daAZ2B_CLlw:APA91bHgQyWp-IBqCWzGJ8NPWs0nmYDK7XsXoCz3awD3hIW9d8K1WBPiQ3q6-QsamtPgPC5B2Qj5zBUlFPasoqLCMc1BFOdUxYjh3rbEeeX7HwErW0ATM-"];
  $devicetoken = get_token();        
  // creación de objeto de clase firebase
  $firebase = new Firebase(); 
   // envío de notificación push y visualización de resultados
  $firebase->send($devicetoken, $mPushNotification);
}

 function get_token()
   {   
    $query = 'daAZ2B_CLlw:APA91bHgQyWp-IBqCWzGJ8NPWs0nmYDK7XsXoCz3awD3hIW9d8K1WBPiQ3q6-QsamtPgPC5B2Qj5zBUlFPasoqLCMc1BFOdUxYjh3rbEeeX7HwErW0ATM-zOF5ncPF1f35LCbSvXQO8-';
    
         $tokens = array(); 
        //while($row=$query->fetch(PDO::FETCH_OBJ)) {
         array_push($tokens, $query);
          //  }
        return $tokens; 
   }
?>


<form id="form1" name="form1" method="POST" action="frmNotificacion.php">
    <input type="submit" name="btn" id="btn" value="enviar">
</form>


