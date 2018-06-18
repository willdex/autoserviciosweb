<?php


	class DbOperation
{
    //Database connection link
    private $con;
 
    //Class constructor
    function __construct()
    {
        // Obtener el archivo DbConnect.php
        require_once dirname(__FILE__) . '/DbConnect.php';
 
        //Creating a DbConnect object to connect to the database
        $db = new DbConnect();
 
        //Initializing our connection link of this class
        //by calling the method connect of DbConnect class
        $this->con = $db->connect();
    }
 
    // almacenamiento de token en la base de datos
    public function registerDevice($email,$token){
        if(!$this->isEmailExist($email)){
            $stmt = $this->con->prepare("INSERT INTO devices (email,token) VALUES (?,?) ");
            $stmt->bind_param("ss",$email,$token);
            if($stmt->execute())
                return 0; //return 0 means success
            return 1; //return 1 means failure
        }else{
            return 2; //returning 2 means email already exist
        }
    }
 
    // comprobará si el correo electrónico ya existe
    private function isEmailexist($email){
        $stmt = $this->con->prepare("SELECT id FROM devices WHERE email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
 
   // obtener todos los tokens para enviar push a todos los dispositivos
    public function getAllTokens(){
        $stmt = $this->con->prepare("SELECT token FROM devices");
        $stmt->execute(); 
        $result = $stmt->get_result();
        $tokens = array(); 
        while($token = $result->fetch_assoc()){
            array_push($tokens, $token['token']);
        }
        return $tokens; 
    }
 
    // obtener un token especificado para enviar push al dispositivo seleccionado
    public function getTokenByEmail($email){
        $stmt = $this->con->prepare("SELECT token FROM devices WHERE email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute(); 
        $result = $stmt->get_result()->fetch_assoc();
        return array($result['token']);        
    }
 
    //obtener todos los dispositivos registrados de la base de datos
    public function getAllDevices(){
        $stmt = $this->con->prepare("SELECT * FROM devices");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result; 
    }
}

?>