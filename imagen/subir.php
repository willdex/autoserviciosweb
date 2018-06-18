<?php

	 
 
	 if($_SERVER['REQUEST_METHOD']=='POST')
	 {
	 
	 	 $image = $_POST['image'];

	 	 require_once('dbConnect.php');
		 $sql = "INSERT INTO img (image) VALUES (?)";
	 
		 $stmt = mysqli_prepare($con,$sql);
	 
		 mysqli_stmt_bind_param($stmt,"s",$image);
		 mysqli_stmt_execute($stmt);
		 
		 $check = mysqli_stmt_affected_rows($stmt);
	 
		 if($check == 1)
		 {
		 echo "Imagen Subida Correctamente";
		 }
		 else
		 {
		 	echo "Error al Subir la Imagen";
		 }
		 mysqli_close($con);
	}
	 else
		 {
		 	echo "Error";
		 }
 