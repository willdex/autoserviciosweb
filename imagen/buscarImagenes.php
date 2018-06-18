<?php



	 $id = $_GET['id'];
	 $sql = "select * from usuario where id = '$id'";
	 require_once('dbConnect.php');
	 
	 $r = mysqli_query($con,$sql);
	 
	 $result = mysqli_fetch_array($r);
	 
	 header('content-type: image/jpeg');
	 
	 echo base64_decode($result['imagen']);
	 
	 mysqli_close($con);
	 


?>