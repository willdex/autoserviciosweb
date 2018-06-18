<?php
require_once  'Basededatos.php';
class Moto extends Database
{
  public function Moto()
  {
    parent::Database();
  }
  public static function set_moto($nombre,$apellido,$ci,$celular,$email,$marca,$modelo,$placa,$direccion,$telefono,$referencia,$codigo,$credito)
  {
   try{

		$consulta="INSERT INTO moto (nombre,apellido,ci,celular,email,marca,modelo,placa,direccion,telefono,referencia,codigo,credito) values(?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$comando=parent::getInstance()->getDb()->prepare($consulta);

		$comando->execute(array($nombre,$apellido,$ci,$celular,$email,$marca,$modelo,$placa,$direccion,$telefono,$referencia,$codigo));
 		return true;
		} catch (PDOException $e) {
		   return false;
		}
  }
 public static function get_moto($telefono,$codigo)
  {$consulta="SELECT * from moto where celular=? and codigo=? and login=0";
  $resultado=-1;
  	try{
  		    $comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($telefono,$codigo));
			$row = $comando->fetch(PDO::FETCH_ASSOC);
      if($row)
         {
          if(self::set_login($row['id'],1))
          { 
           $row=self::get_moto2($telefono,$codigo);
           if($row!="-1")
           {
            $resultado=$row;
           }
          }
        }
        
  	}catch(PDOException $e)
  	{
  		$resultado=-1;
  	}
    return $resultado;
  }
  // busca motos solo con login iniciado...
 public static function get_moto2($telefono,$codigo)
  { $consulta="SELECT * from moto where celular=? and codigo=? and login=1";
  $resultado=-1;
    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($telefono,$codigo));
      $row = $comando->fetch(PDO::FETCH_ASSOC);
           $resultado=$row; 
    }catch(PDOException $e)
    {
      $resultado=-1;
    }
    return $resultado;
  }
 public static function get_motos_en_rango($latitud,$longitud,$diametro)
  {
    try{
    $consulta="SELECT *,distancia_entre_dos_puntos(latitud,longitud,?,?) as 'distancia' from moto where distancia_entre_dos_puntos(latitud,longitud,?,?)<= ? and estado='1'";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($latitud,$longitud,$latitud,$longitud,$diametro));
    $row = $comando->fetchAll();
    if($row)
        return $row;
      else
        return -1;
  }catch(PDOException $e)
  {
    return -1;
  }
    
  }

 public static function set_ubicacion_punto($latitud,$longitud,$id_moto)
  {
    try{
    $consulta="UPDATE moto set latitud= ? , longitud= ?  where id= ? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($latitud,$longitud,$id_moto));
    return true;
    }catch(PDOException $e)
    {
      return false;
    }

  }
 public static function set_puntos($latitud,$longitud,$id_pedido,$id_carrera,$numero)
  {
  try{
    $consulta="INSERT ruta (latitud,longitud,id_pedido,id_carrera,numero)values(?,?,?,?,?)";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($latitud,$longitud,$id_pedido,$id_carrera,$numero));
    return true;
    }catch(PDOException $e)
    {
      return false;
    }
  }
 public static function set_estado($estado,$id_moto)
  {
    try{
      $consulta="UPDATE moto set estado= ? where id= ? ";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($estado,$id_moto));

      return true;
    }catch(PDOException $e)
    {
      return false;
    }
  }
 public static function set_login($id_moto,$login)
  {
  
    try{
      $consulta="UPDATE moto  set login= ?,estado= ?,token='' where id= ?";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($login,$login,$id_moto));

        return true;
    }catch(PDOException $e)
      {
        return false;
      }
  }
 public static function get_login($id_moto)
  { $consulta="SELECT login from moto where id= ?";
  $resultado=-1;
    try{
     
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_moto));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        $resultado=$row;
      }
      
    }catch(PDOException $e)
    {
      $resultado=-1;
    }
    return $resultado;
  }

 public static function obtener_ubicacion_por_id_pedido($id_pedido)
  {
    $consulta="select m.latitud, m.longitud,p.id as 'id_pedido',p.estado from pedido p,moto m where p.id_moto=m.id and p.id=?";
    $resultado="-1";
    try{
       $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      $resultado=$row;
    }catch(PDOException $e)
    {
      $resultado="-1";
    }
    return $resultado;
  }

 public static function obtener_ubicacion_por_id_pedido_carrera($id_pedido)
  {
       $consulta="select c.id as 'id_carrera',m.latitud,m.longitud,p.estado from pedido p, carrera c, moto m where p.id=c.id_pedido and c.id_moto=m.id and c.id_pedido=? order by c.id desc limit 1";
    $resultado="-1";
    try{
       $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      $resultado=$row;
    }catch(PDOException $e)
    {
      $resultado="-1";
    }
    return $resultado;
  }

 public static function set_token($celular,$token)
  {
    $consulta="UPDATE moto set token=? where celular=?";
    try{
       $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($token,$celular));
      return true;
    }catch(PDOException $e)
    {
       return false;
    }
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

  public static function get_imagen($id_moto)
  {
    try{
      $consulta="SELECT * from moto where id=".$id_moto;
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute();
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      return $row['imagen'];
    }catch(PDOException $e)
    {
      return -1;
    }
  }

public static function get_imagen_usuario($id_usuario)
  {
    try{
      $consulta="SELECT * from usuario where id=".$id_usuario;
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute();
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      return $row['imagen'];
    }catch(PDOException $e)
    {
      return -1;
    }
  }
  

}
?>