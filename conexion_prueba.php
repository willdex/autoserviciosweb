<?php
// Conectando, seleccionando la base de datos
$link = mysqli_connect('104.154.143.24', 'root', 'Alonzo123-')
    or die('No se pudo conectar: ' . mysql_error());
echo 'Connected successfully';
mysqli_select_db('Asapp') or die('No se pudo seleccionar la base de datos');
?>