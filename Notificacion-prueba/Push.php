<?php

class Push //Almacena las Notificaciones
{
    //notificacion  titulo
    private $title;
  
    //notification message 
    private $message;
    
    //notification image url 
    private $image;
 
    //initializing values in this constructor
    function __construct($title, $message, $image) {
         $this->title = "CARRERA DISPONIBLE";
         $this->message = "carrera disponible cerca de TI..!"; 
         $this->image = $image; 
    }
    
    // obtener la notificación push
    public function getPush() {
        $res = array();
        $res['data']['title'] = $this->title;
        $res['data']['message'] = $this->message;
        $res['data']['image'] = $this->image;
        return $res;

       
    }
}
/*
La clace inicializa las variables necesarias 
para empujar en el constructor, y nos devuelve una matriz con los datos necesarios en el método getPush ().

*/

?>

