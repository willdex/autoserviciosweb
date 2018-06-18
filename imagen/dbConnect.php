<?php
 define('HOST','localhost');
 define('USER','root');
 define('PASS','12345678a');
 define('DB','easymoto');
 
 $con = mysqli_connect(HOST,USER,PASS,DB) or die('error al conectarse');