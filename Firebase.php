<?php

     //Para enviar la notificación push necesitamos hacer la solicitud http al servidor firebase. 
    //Curl es una librería de funciones para conectar con servidores
class Firebase {
 
    public function send($registration_ids, $message) {
        $fields = array(
            'registration_ids' => $registration_ids,
            'data' => $message,
        );
        return $this->sendPushNotification($fields);
    }
    
    
   // Esta función hará que la solicitud curl real al servidor firebase  y luego el mensaje sea enviado 

    private function sendPushNotification($fields) {
         
        //importing  constant files
        
        
        //firebase server url to send the curl request
        $url = 'https://fcm.googleapis.com/fcm/send';
 
        //building headers for the request
        $headers = array(
            'Authorization: key=AAAAX8MTcYQ:APA91bHeiS-jcCTRNRXwM0Yp4ff1c42_t3P6VNXs5zN7fK8FoJ4-SdqvCz2PDISgm6tyEmbpPZT5znwpxdRm_q4raQZJPvega--Va6vqNGuOJn3noJLNYd5X3t6cuGPTOlqOba0trIo2',
            'Content-Type: application/json'
        );
 
        //Initializing curl to open a connection
        $ch = curl_init();
 
        //Setting the curl url
        curl_setopt($ch, CURLOPT_URL, $url);
        
        //setting the method as post
        curl_setopt($ch, CURLOPT_POST, true);
 
        //adding headers 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        //disabling ssl support
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        //adding the fields in json format 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        //finally executing the curl request 
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
 
        //Now close the connection
        curl_close($ch);
 
        //and return the result 
        return $result;
    }
}
?>