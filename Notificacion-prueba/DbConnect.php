<?php


	//Class DbConnect
class DbConnect
{
    // Variable para almacenar el enlace de la base de datos
    private $con;
 
    //Class constructor
    function __construct()
    {
 
    }
 
    // Este método se conectará a la base de datos
    function connect()
    {
        // Incluye el archivo config.php para obtener las constantes de la base de datos
        include_once dirname(__FILE__) . '/config.php';
 
        //connecting to mysql database
        $this->con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
        //si hay error en la conexion
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
 
       
        return $this->con;
    }
 
}

?>