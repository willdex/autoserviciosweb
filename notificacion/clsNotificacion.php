<?php
include_once 'Push.php';
include_once 'Firebase.php';

class Notificacion 
{
  public function notificacion(){
      	try{

           $push = new Push('Asapp','Mensaje',"20");

             $mPushNotification = $push->getPush(); 
             // obtener el token del objeto de base de datos

        $devicetoken=["daAZ2B_CLlw:APA91bHgQyWp-IBqCWzGJ8NPWs0nmYDK7XsXoCz3awD3hIW9d8K1WBPiQ3q6-QsamtPgPC5B2Qj5zBUlFPasoqLCMc1BFOdUxYjh3rbEeeX7HwErW0ATM-"];
             //$devicetoken = self::get_token_moto($latitud,$longitud);        

            // creación de objeto de clase firebase
             $firebase = new Firebase(); 
            
             // envío de notificación push y visualización de resultados
              $firebase->send($devicetoken, $mPushNotification);
                return true;
            }
        catch (Exception $e){
            return false;
        }
    } 
}




?>