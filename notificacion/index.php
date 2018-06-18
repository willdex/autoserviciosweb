<?php 


function send_notificacion($tokens, $message)
{
    $url = 'https://fcm.googleapis.com/fcm/send';
	$fields = array('registration_ids' => $tokens,
					'data' => $message
					);
	
	$headers = array(
            'Authorization: key=AIzaSyBTMUxbM3EGbb5ErJZqHjeKmd5avXox4Nc',
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
 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);//este aumente

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

	$conn = mysqli_connect("localhost","root","123456a","asapp");
	$sql = "SELECT token FROM notificacion";
	$result = mysqli_query($conn,$sql);
	$tokens = array();
	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$tokens[] = $row["token"];
		}
	}
	echo var_dump($tokens);
	mysqli_close($conn);

	$message = array("message" => "hola mundo");
	$message_status = send_notificacion($tokens, $message);
	echo $message_status;
 ?>