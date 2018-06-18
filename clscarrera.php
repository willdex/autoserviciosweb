<?php

require_once('Basededatos.php');
include_once 'Push.php';
include_once 'Firebase.php';

class Carrera extends Database
{
  public function Carrera()
  {
    parent::Database();
  }
  function iniciar_carrera($direccion_inicio,$opcion,$id_pedido,$id_usuario,$id_moto)
  {
   try{
		$consulta="INSERT INTO carrera (direccion_inicio,opciones,id_pedido,id_usuario,id_moto,fecha_fin) values(?,?,?,?,?,null)";
		$comando=parent::getInstance()->getDb()->prepare($consulta);

		$comando->execute(array($direccion_inicio,$opcion,$id_pedido,$id_usuario,$id_moto));
     $lastId = parent::getInstance()->getDb()->lastInsertId();

     //actualizar estado en modo de carrera.
     $consulta="UPDATE pedido set estado=1 where id= ? and estado<2";
     $comando=parent::getInstance()->getDb()->prepare($consulta);
     $comando->execute(array($id_pedido));

    if(self::verificar_estado_del_pedido($id_pedido)==3)
    {
      self::eliminar_carrera($lastId);
      //-2 a sido cancelado el pedido
      return -2;
    }   
    else
    {



      // fin de carrera
          return $lastId;
     }
		} catch (PDOException $e) {

		   return -1;
		}
  }

 function get_cantidad_carrera($id_pedido)
  {$consulta="SELECT count(*)as cantidad from carrera where id_pedido=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row['cantidad'];
      }
      else
      {
        return 0;
      }
    }catch(PDOException $e)
    {
      return 0;
    }
  } 
function verificar_estado_del_pedido($id_pedido)
  {$consulta="SELECT estado from pedido where id=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row['estado'];
      }
      else
      {
        return -1;
      }
    }catch(PDOException $e)
    {
      return -1;
    }
  }

 function eliminar_carrera($id_carrera)
  {$consulta="DELETE from carrera where id=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_carrera));

      return true;
    }catch(PDOException $e)
    {
      return false;
    }
  }


 function terminar_carrera($direccion_fin,$distancia,$opcion,$id_pedido,$id_usuario,$id_moto,$id)
  {$ruta=self::get_ruta_por_id_carrera($id);    

      $tarifa=self::get_tarifa($distancia);
      $monto_total=$tarifa['monto'];
      $porcentaje_moto=$tarifa['porcentaje_moto'];
      $porcentaje_empresa=$tarifa['porcentaje_empresa'];
      $costo_fijo_moto=$tarifa['costo_fijo_moto'];
      $gasto_fijo_empresa=$tarifa['gasto_fijo_empresa'];
      $impuesto=$tarifa['impuesto'];

      $monto_motista=self::valor_por_porcentaje_monto($porcentaje_moto,($monto_total-$costo_fijo_moto));
      $monto_empresa=$monto_total+$gasto_fijo_empresa+$impuesto;

    $consulta="UPDATE carrera  set direccion_fin=? ,distancia=?,opciones=?,monto=?,monto_motista=?,monto_empresa=?,fecha_fin=now(),ruta= ?,porcentaje_moto=?,porcentaje_empresa=?,costo_fijo_moto=?,gasto_fijo_empresa=?,impuesto=? where id_pedido=? and id_usuario=? and id_moto=? and id=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($direccion_fin,$distancia,$opcion,$monto_total,$monto_motista,$monto_empresa,$ruta,$porcentaje_moto,$porcentaje_empresa,$costo_fijo_moto,$gasto_fijo_empresa,$impuesto,$id_pedido,$id_usuario,$id_moto,$id));
      //obtener token para enviar la finnalizacion de la carrera..
     // $token=self::get_token_id_pedido($id_pedido);
     // self::enviar_notificacion_de_finalizacion_de_carrera($token,$id_pedido);
//obtenermos las cantidades de carreras.
      $cantidad=self::get_cantidad_carrera($id_pedido);

     //obtener token para enviar la finnalizacion de la carrera..
      $token=self::get_token_id_pedido($id_pedido);
      self::enviar_notificacion_de_fin_de_carrera($token,$id_pedido,$cantidad);
       self::sumar_credito($monto_motista,$id_moto);
      return true;
    }catch(PDOException $e)
    {
      return false;
    }
  }

 function terminar_carrera_pedido($direccion_fin,$distancia,$opcion,$id_pedido,$id_usuario,$id_moto,$id)
  {
    $ruta=self::get_ruta_por_id_carrera($id);   
   
     $tarifa=self::get_tarifa($distancia);
      $monto_total=$tarifa['monto'];
      $porcentaje_moto=$tarifa['porcentaje_moto'];
      $porcentaje_empresa=$tarifa['porcentaje_empresa'];
      $costo_fijo_moto=$tarifa['costo_fijo_moto'];
      $gasto_fijo_empresa=$tarifa['gasto_fijo_empresa'];
      $impuesto=$tarifa['impuesto'];

      $monto_motista=self::valor_por_porcentaje_monto($porcentaje_moto,($monto_total-$costo_fijo_moto));
      $monto_empresa=$monto_total+$gasto_fijo_empresa+$impuesto;

    $consulta="UPDATE carrera  set direccion_fin='$direccion_fin' ,distancia='$distancia',opciones='$opcion',monto='$monto_total',monto_motista='$monto_motista',monto_empresa='$monto_empresa',fecha_fin=now(),ruta= '$ruta',porcentaje_moto='$porcentaje_moto',porcentaje_empresa='$porcentaje_empresa',costo_fijo_moto='$costo_fijo_moto',gasto_fijo_empresa='$gasto_fijo_empresa',impuesto='$impuesto' where id_pedido='$id_pedido' and id_usuario='$id_usuario' and id_moto='$id_moto' and id='$id'";


    try{
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute();
 //el credido es el monto total a pagar al motista.. se suma el el monto_motista registrado en la carrera a credito actual.
       self::sumar_credito($monto_motista,$id_moto);
   //TERMINAR TODO EL PEDIDO es donde se actualiza el estado dek pedido. a  2. 
        self::terminar_todo_pedido($id_pedido);
              return true;
    }catch(PDOException $e)
    {
      echo $e;
      return false;
    }
  }

//obtener el valor con los parametros de porcentaje(%) y el valor.

public function valor_por_porcentaje_monto($porcentaje,$monto_bruto)
{$monto=($monto_bruto*$porcentaje)/100;
return $monto;
}
public static function sumar_credito($cantidad,$id_moto)
{
$consulta="UPDATE pedido SET credito=credito+".$cantidad." where id=".$id_moto;
      try{
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute();
        return true;        
      }catch(PDOException $e)
      {
           return false;
      }
}
//cambiar el estado de la tabla de pedido.. estado=2
public static function terminar_todo_pedido($id_pedido)
  {
      $monto_total=self::get_monto_total_por_id_pedido($id_pedido);
      $monto_empresa=self::get_monto_empresa_por_id_pedido($id_pedido);
      $monto_motista=self::get_monto_motista_por_id_pedido($id_pedido);

  
    $consulta="UPDATE pedido SET estado='2',fecha_llegado=now(),monto_total=?,monto_empresa=?,monto_empresa_aux=?,monto_motista=?,monto_motista_aux=? where id=?";
      try{
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute(array($monto_total,$monto_empresa,$monto_empresa,$monto_motista,$monto_motista,$id_pedido));
        //enviamos notificacion para finalizar el pedido
        self::notificacion_terminar_todo_pedido($id_pedido,$monto_empresa);


        return true;
        
      }catch(PDOException $e)
      {
           return false;
      }
  }

  public static function get_credito_por_id_moto($id_moto)
  {$credito=0;
      try{
          $consulta="SELECT  credito from moto where id=?";
           $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array( $id_moto));
          $row=$comando->fetch(PDO::FETCH_ASSOC);
            if($row)
            {
              $credito=$row['credito'];
            }
      }catch(PDOException $e)
      {

      }
    return $credito;
  }

// enviar notificacion de finalizacion de pedido
public static function notificacion_terminar_todo_pedido($id_pedido,$monto_total)
   { //esta funcion registra el id de la moto en el pedido que acaba de aceptar.......y si el pedio ya a sido registrado entonces devuelve que no se puede registrar...
    $res=false;
        
    try{
      $token=self::get_token_id_pedido($id_pedido);
            
       $push = new Push('Pedido','Carreras finalizadas.',null,"usuario",$id_pedido,"","","","5");
     
      if($monto_total!="" && $monto_total!="NULL" && $monto_total!="null")
      {
       $push->set_monto_total($monto_total);
      }
       // obteniendo el empuje del objeto push
       $mPushNotification = $push->getPush(); 
       
       // obtener el token del objeto de base de datos

     

      // creación de objeto de clase firebase
       $firebase = new Firebase(); 
       
       // envío de notificación push y visualización de resultados
        $firebase->send($token, $mPushNotification);
      return true;
      }
    catch (Exception $e){
  return false;
    }
   }
  //notificaciones......
      public static function enviar_notificacion_de_finalizacion_de_carrera($token,$id_pedido)
    {try{
       $push = new Push('Pedido','Carrera terminada.',null,"usuario",$id_pedido,"","","","1");
       // obteniendo el empuje del objeto push
       
       $mPushNotification = $push->getPush(); 
       // obtener el token del objeto de base de datos

       $devicetoken = $token;    

      // creación de objeto de clase firebase
       $firebase = new Firebase(); 
       
       // envío de notificación push y visualización de resultados
        $firebase->send($devicetoken, $mPushNotification);
      return true;
      }
    catch (Exception $e){
  return false;
    }
    }



 //notificaciones......
      public static function enviar_notificacion_de_fin_de_carrera($token,$id_pedido,$cantidad)
    {try{
      if($cantidad==0)
      {
       $push = new Push('Pedido','Se termino su carrera.',null,"usuario",$id_pedido,"","","","12");
     }
     else if($cantidad>10)
     {
       $push = new Push('Pedido','Se inicio su carrera numero '.$cantidad.'.',null,"usuario",$id_pedido,"","","","12");
     }
     else if($cantidad>0 && $cantidad<=10 )
     {$letra=self::get_numero_a_letra($cantidad);
       $push = new Push('Pedido',$letra.' destino concluido.',null,"usuario",$id_pedido,"","","","12");
     }
       // obteniendo el empuje del objeto push
       
       $mPushNotification = $push->getPush(); 
       // obtener el token del objeto de base de datos

       $devicetoken = $token;    

      // creación de objeto de clase firebase
       $firebase = new Firebase(); 
       
       // envío de notificación push y visualización de resultados
        $firebase->send($devicetoken, $mPushNotification);
      return true;
      }
    catch (Exception $e){
  return false;
    }
    }

  function set_tarifa($id_tarifa,$id_pedido,$id_usuario,$id_moto,$id)
  {
  	$consulta="UPDATE carrera  set id_tarifa=? where id_pedido=? and id_usuario=? and id_moto=? and id=?";
  	try{
  		    $comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_tarifa,$id_pedido,$id_usuario,$id_moto,$id));
			return true;
  	}catch(PDOException $e)
  	{
  		return false;
  	}
  }

  function get_carrera_en_curso($id_pedido)
  {$resultado=-1;
    $consulta ="SELECT id,direccion_inicio,direccion_fin,distancia,opciones,fecha_inicio as 'fecha1',fecha_fin as 'fecha2',id_pedido,id_usuario,id_moto,id_tarifa,monto from carrera where  id_pedido= ?  order by id desc limit 1";
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        $resultado=$row;
      }
      
    }
    catch(PDOException $e)
    {
      $resultado=-1;
    }
    return $resultado;

  }
  function get_direccion_por_id($id_direccion)
  {
    $consulta="SELECT * from direccion where id=?";
  $resultado=-1;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_direccion));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
        { $resultado= $row;}

    }
    catch(PDOException $e)
    {
       $resultado=-1;
    }
    return $resultado;
  }
  function existe_direccion($latitud,$longitud,$id_usuario)
  {
     $consulta="SELECT * from direccion where latitud=? and longitud=? and id_usuario=?";
  $resultado=-1;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($latitud,$longitud,$id_usuario));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
        { $resultado= $row;}

    }
    catch(PDOException $e)
    {
       $resultado=-1;
    }
    return $resultado;
  }
  function insertar_direccion($latitud,$longitud,$detalle,$id_usuario)
  { 
     $direccion=self::existe_direccion($latitud,$longitud,$id_usuario);

     if($direccion=="-1")
     {
       $consulta="INSERT INTO direccion (nombre,detalle,latitud,longitud,id_usuario) values('','".$detalle."','".$latitud."','".$longitud."','".$id_usuario."')";
       
        try{
              $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($detalle,$latitud,$longitud,$id_usuario));
           $lastId = parent::getInstance()->getDb()->lastInsertId();
  
          return $lastId;
        }catch(PDOException $e)
        {
          return -1;
        }
     }
     else
     {
      return $direccion['id'];
     }
  }

  function lista_de_carrera_por_usuario($id_usuario,$id_pedido)
  {
    $consulta="SELECT c.id,da.detalle as 'detalle_inicio',da.latitud as 'latitud_inicio',da.longitud as 'longitud_inicio',db.detalle as 'detalle_fin',db.latitud as 'latitud_fin',db.longitud as 'longitud_fin',c.distancia,c.opciones,c.fecha_inicio,c.fecha_fin,c.id_pedido,c.id_usuario,c.id_moto,c.monto ,c.ruta as 'ruta' from carrera c, direccion da,direccion db where  c.direccion_inicio=da.id and c.direccion_fin=db.id and c.id_usuario=? and c.id_pedido=?";
    $resultado=-1;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_usuario,$id_pedido));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= -1;
        }
        return $resultado;
  }


   function get_ruta_por_id_carrera($id_carrera)
  {
    $consulta="SELECT latitud,longitud FROM ruta where id_carrera=? order by numero asc";
    $query = parent::getInstance()->getDb()->prepare($consulta);
        $query->execute(array($id_carrera)); 
       $ruta="";
       $inicio="markers=color:red|label:I";
       $fin="";
       $punto="";
       $latitud=0;
       $longitud=0;
       $dato="";
       $auxiliar="";
       $recorrido="path=color:0x0000ff|weight:5";
       $sw_punto=false;

$primera=$query->fetch(PDO::FETCH_ASSOC);
$inicio = $inicio."|".$primera['latitud'].",".$primera['longitud'];
$recorrido = $recorrido."|".$primera['latitud'].",".$primera['longitud'];

        while($row=$query->fetch(PDO::FETCH_OBJ)) {
           $latitud = $row->latitud;
           $longitud = $row->longitud;
           $dato=$latitud.",".$longitud;
             
             if($auxiliar!=$dato) {
                 $auxiliar=$dato;
                 $recorrido = $recorrido."|".$latitud.",".$longitud;
                 $fin = "|".$latitud.",".$longitud;
                  $sw_punto = true;
                        }
      
    }
     $fin = "markers=color:blue|label:F".$fin;
    $ruta="https://maps.googleapis.com/maps/api/staticmap?size=600x400&scale=2&maptype=roadmap&".$inicio."&".$fin."&".$recorrido;
                 
        return $ruta; 
  }

   function lista_de_carrera_por_moto($id_moto,$id_pedido)
  {
    $consulta="SELECT c.id,da.detalle as 'detalle_inicio',da.latitud as 'latitud_inicio',da.longitud as 'longitud_inicio',db.detalle as 'detalle_fin',db.latitud as 'latitud_fin',db.longitud as 'longitud_fin',c.distancia,c.opciones,c.fecha_inicio,c.fecha_fin,c.id_pedido,c.id_usuario,c.id_moto,c.monto, c.ruta as 'ruta' from carrera c, direccion da,direccion db where c.direccion_inicio=da.id and c.direccion_fin=db.id and c.id_moto=? and c.id_pedido=?";
    $resultado=-1;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_moto,$id_pedido));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= -1;
        }
        return $resultado;
  }
  function insertar_casa($detalle,$latitud,$longitud,$id_usuario)
  {$resultado=false;
    $id=self::insertar_direccion($latitud,$longitud,$detalle,$id_usuario);
    if($id)
    {
        try{
        $consulta="UPDATE from usuario set id_casa=? where id=?";
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute(array($id,$id_usuario));
        $resultado=true;
      }catch(PDOException $e)
      {
        $resultado=false;
      }
    }
    return $resultado;
  }
  function insertar_oficina($detalle,$latitud,$longitud,$id_usuario)
  {$resultado=false;
    $id=self::insertar_direccion($latitud,$longitud,$detalle,$id_usuario);
    if($id)
    {
        try{
        $consulta="UPDATE from usuario set id_oficina=? where id=?";
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute(array($id,$id_usuario));
        $resultado=true;
      }catch(PDOException $e)
      {
        $resultado=false;
      }
    }
    return $resultado;
  }
  function insertar_trabajo($detalle,$latitud,$longitud,$id_usuario)
  {$resultado=false;
    $id=self::insertar_direccion($latitud,$longitud,$detalle,$id_usuario);
    if($id)
    {
        try{
        $consulta="UPDATE from usuario set id_trabajo=? where id=?";
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute(array($id,$id_usuario));
        $resultado=true;
      }catch(PDOException $e)
      {
        $resultado=false;
      }
    }
    return $resultado;
  }

//obtiene los puntos de la direccion final de cada carrera  q pertenece a un solo pedido......
  function  lista_de_carreras_por_id_pedido($id_pedido)
  {
    $consulta="select d.* from direccion d,carrera c where d.id=c.direccion_fin and c.id_pedido=?";
    $resultado=-1;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_pedido));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= -1;
        }
        return $resultado;
  }

  function existe_carrera_por_id_pedido($id_pedido)
  {
    $consulta="select * from carrera where id_pedido=?";
    $resultado=false;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_pedido));
          $row=$comando->fetch(PDO::FETCH_ASSOC);  
          if($row)
          {
            $resultado=true;
          }
          
        }catch(PDOException $e)
        {
          $resultado= false;
        }
        return $resultado;

  }

function rutas_por_id_usuario($id_usuario)
  {
    $consulta="select r.* from pedido p, ruta r where p.id=r.id_pedido and p.id_usuario=? order by numero asc;";
    $resultado=false;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_usuario));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= false;
        }
        return $resultado;

  }
function rutas_por_id_moto($id_moto)
  {
    $consulta="select r.* from pedido p, ruta r where p.id=r.id_pedido and p.id_moto=? order by numero asc";
    $resultado=false;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_moto));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= false;
        }
        return $resultado;

  }
  ///funcion para obtener el token del usuario en base a un pedido

    public static function get_token_id_pedido($id_pedido)
   { //obtenemos el token del usuario.
    $query = parent::getInstance()->getDb()->prepare("SELECT u.token from usuario u, pedido p where u.id=p.id_usuario and p.id=?");
        $query->execute(array($id_pedido)); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
      array_push($tokens, $row->token);
    }
        return $tokens; 
   }

   public static function get_numero_a_letra($numero)
   {$letra=$numero;
    switch ($numero) {
      case 1:
        $letra="primera";
        break;
      case 2:
        $letra="segunda";
        break;
      case 3:
        $letra="tercera";
        break;
      case 4:
        $letra="cuarta";
        break;
      case 5:
        $letra="quinta";
        break;      
      case 6:
        $letra="sexta";
        break;   
      case 7:
        $letra="septima";
        break;
      case 8:
        $letra="octava";
        break;
      case 9:
        $letra="novena";
        break;
      case 10:
        $letra="decima";
        break;

    }
    return $letra;
   }

   public static function get_ultima_tarifa()
  {
    try{
      $consulta="SELECT * from tarifa order by id desc limit 1";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute();
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      return $row;
    }catch(PDOException $e)
    {
      return -1;
    }
  }

  function get_monto_total_por_id_pedido($id_pedido)
  {$resultado=-1;
    $consulta ="select sum(c.monto)as 'monto_total' from carrera c,pedido p where p.id=c.id_pedido and p.id=?";
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        $resultado=$row['monto_total'];
      }
      
    }
    catch(PDOException $e)
    {
      $resultado=-1;
    }
    return $resultado;

  }
   function get_monto_motista_por_id_pedido($id_pedido)
  {$resultado=-1;
    $consulta ="select sum(c.monto_motista)as 'monto_motista' from carrera c,pedido p where p.id=c.id_pedido and p.id=?";
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        $resultado=$row['monto_motista'];
      }
      
    }
    catch(PDOException $e)
    {
      $resultado=-1;
    }
    return $resultado;

  }
  function get_monto_empresa_por_id_pedido($id_pedido)
  {$resultado=-1;
    $consulta ="select sum(c.monto_empresa)as 'monto_empresa' from carrera c,pedido p where p.id=c.id_pedido and p.id=?";
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        $resultado=$row['monto_empresa'];
      }
      
    }
    catch(PDOException $e)
    {
      $resultado=-1;
    }
    return $resultado;

  }
function get_tarifa($distancia)
  {$consulta="SELECT * from tarifa where distancia>=? limit 1";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($distancia));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row;
      }
      else
      {
        return -1;
      }
    }catch(PDOException $e)
    {
      return -1;
    }
  }

  function get_distancia($id_carrera,$latitud,$longitud)
{
$comando= parent::getInstance()->getDb()->prepare("SELECT distancia_entre_dos_puntos(?,?,d.latitud,d.longitud) as distancia FROM carrera c,direccion d where  c.id=? and c.direccion_inicio=d.id");
         $comando->execute(array($latitud, $longitud,$id_carrera)); 
       $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row['distancia'];
      }
      else
      {
        return 0;
      }
}


}




?>