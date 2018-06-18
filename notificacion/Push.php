<?php

class Push //Almacena las Notificaciones
{
    //notificacion  titulo
    private $title;
  
    //notification message 
    private $message;
    
    //notification image url 
    private $image;
    // tipo_cliente ...1 moto,   2 usuario.
    private $cliente;
    private $id_pedido;
    private $nombre;
    private $latitud;
    private $longitud;
    private $tipo;
    private $pedido;
    private $fecha;
    private $hora;
    private $id_empresa;
    private $empresa;
    private $nombre_direccion;
    private $detalle_direccion;
     private $monto_total;
 
 
    //initializing values in this constructor
   
    

 function __construct($title, $message, $image,$cliente,$id_pedido,$nombre,$latitud,$longitud,$tipo) {
         $this->title = $title;
         $this->message =$message ; 
         $this->image = $image; 
         $this->cliente=$cliente;
         $this->id_pedido=$id_pedido;
         $this->nombre=$nombre;
         $this->latitud=$latitud;
         $this->longitud=$longitud;
         $this->tipo=$tipo;
          $this->pedido="";
          $this->empresa="";
         date_default_timezone_set("America/La_Paz") ;
         $this->fecha =date("d-m-Y",time());
         $this->hora=date("H:i:s",time());
         $this->id_empresa="";
         $this->nombre_direccion="";
         $this->detalle_direccion="";
         $this->monto_total="";

    }
    

     public function set_pedido($pedido) {
         $this->pedido=$pedido;
    }
    public function set_id_empresa($id_empresa) {
         $this->id_empresa=$id_empresa;
    }

    public function set_empresa($empresa) {
         $this->empresa=$empresa;
    }
    public function set_nombre_direccion($nombre_direccion) {
         $this->nombre_direccion=$nombre_direccion;
    }
    public function set_detalle_direccion($detalle_direccion) {
         $this->detalle_direccion=$detalle_direccion;
    }
     public function set_monto_total($monto_total) {
         $this->monto_total=$monto_total;
    }
    // obtener la notificación push
    
    public function getPush() {
        $res = array();
        $res['data']['title'] = $this->title;
        $res['data']['message'] = $this->message;
        $res['data']['image'] = $this->image;
        $res['data']['cliente'] = $this->cliente;
        $res['data']['id_pedido'] = $this->id_pedido;
         $res['data']['nombre'] = $this->nombre;
          $res['data']['latitud'] = $this->latitud;
           $res['data']['longitud'] = $this->longitud;
           $res['data']['tipo'] = $this->tipo;
            $res['data']['pedido'] = $this->pedido;
           $res['data']['fecha'] = $this->fecha;
           $res['data']['hora'] = $this->hora;
            $res['data']['id_empresa'] = $this->id_empresa;
             $res['data']['empresa'] = $this->empresa;
             $res['data']['nombre_direccion'] = $this->nombre_direccion;
             $res['data']['detalle_direccion'] = $this->detalle_direccion;
               $res['data']['monto_total'] = $this->monto_total;
        return $res; 
    }
}
/*
La clace inicializa las variables necesarias 
para empujar en el constructor, y nos devuelve una matriz con los datos necesarios en el método getPush ().

*/

?>

