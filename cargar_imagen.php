<?
$link = mysql_connect('127.0.0.1', 'root', '');
if (!$link)
die('Error al conectarse con MySQL: ' . mysql_error().' <br>Número del error: '.mysql_errno());
if (! @mysql_select_db("easymoto",$link)){
echo "No se pudo conectar correctamente con la Base de datos";
exit();
}


$image = imagecreatefromgif('http://www.construyehogar.com/wp-content/uploads/2015/06/Dise%C3%B1o-de-casa-moderna-de-dos-plantas.jpg'); 
ob_start(); 
imagegif($image); 
$jpg = ob_get_contents();
ob_end_clean();
echo $jpg;

$jpg = str_replace('##','##',mysql_escape_string($jpg));
$result = mysql_query("INSERT INTO carrera (imagen)values('$jpg')");

$result = mysql_query("SELECT imagen FROM carrera WHERE id=8");
$result_array = mysql_fetch_array($result);
header("Content-Type: image/gif");
echo $result_array[0];
?>