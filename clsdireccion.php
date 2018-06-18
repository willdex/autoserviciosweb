<?php
require('Basededatos.php');
class Direccion extends Database
{
  public function Direccion()
  {
    parent::Database();
  }
public static  function set_direccion($detalle,$latitud,$longitud,$id_empresa,$id_usuario)
  {

   try{

    if($id_empresa)
    {
      $consulta="INSERT INTO direccion (detalle,latitud,longitud,id_empresa,id_usuario) values(?,?,?,?,?)";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($detalle,$latitud,$longitud,$id_empresa,$id_usuario));
    }
    else
    {
      $consulta="INSERT INTO direccion (detalle,latitud,longitud,id_usuario) values(?,?,?,?)";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($detalle,$latitud,$longitud,$id_usuario));
    }
 		return true;
		} catch (PDOException $e) {
		   return false;
		}
  }
  function get_direccion($id,$id_usuario)
  {$consulta="SELECT * from direccion where id=? and id_usuario=?";
  	try{
  		    $comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id,$id_usuario));
			$row = $comando->fetchAll();
      if($row)
      {
        return $row;
      }
      else
			return -1;
  	}catch(PDOException $e)
  	{
  		return -1;
  	}
  }
  function get_direccion_por_latitud_longitud($latitud,$longitud)
  {
    $consulta="SELECT * from direccion where latitud= ? and longitud=?";
    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($latitud,$longitud));
      $row = $comando->fetchAll();
      return $row;
    }catch(PDOException $e)
    {
      return -1;
    }
  }
  function get_direccion_por_id_usuario($id_usuario,$id_empresa)
  {
    $consulta="SELECT * from direccion where nombre<>'' and id_usuario=? and id_empresa=?";

    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_usuario,$id_empresa));
      $row=$comando->fetchAll();

      if($row)
        {return $row;}
      else
        {return -1;}
    }
    catch(PDOException $e)
    {
      return -1;
    }
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
  function insertar_direccion($latitud,$longitud,$nombre,$detalle,$id_usuario)
  { 
   
       $consulta="INSERT into direccion (nombre,detalle,latitud,longitud,id_usuario) values(?,?,?,?,?)";
        try{
              $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($nombre,$detalle,$latitud,$longitud,$id_usuario));
           $lastId = parent::getInstance()->getDb()->lastInsertId();
          return $lastId;
        }catch(PDOException $e)
        {
          return -1;
        }
     
  }
   function insertar_direccion_empresa($latitud,$longitud,$nombre,$detalle,$id_usuario,$id_empresa)
  { 
   
       $consulta="INSERT into direccion (nombre,detalle,latitud,longitud,id_usuario,id_empresa) values(?,?,?,?,?,?)";
        try{
              $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($nombre,$detalle,$latitud,$longitud,$id_usuario,$id_empresa));
           $lastId = parent::getInstance()->getDb()->lastInsertId();
          return $lastId;
        }catch(PDOException $e)
        {
          return -1;
        }
     
  }
     function modificar_direccion_empresa($latitud,$longitud,$nombre,$detalle,$id_usuario,$id_empresa,$id_direccion)
  { 
        $consulta="UPDATE direccion set nombre=?,detalle=?,latitud=?,longitud=? where id=? and id_usuario=? and id_empresa=?";
        try{
              $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($nombre,$detalle,$latitud,$longitud,$id_direccion,$id_usuario,$id_empresa));
          return true;
        }catch(PDOException $e)
        {
          return false;
        }
     
  }

   function insertar_casa($nombre,$detalle,$latitud,$longitud,$id_usuario)
  {$resultado=-1;
    $id=self::insertar_direccion($latitud,$longitud,$nombre,$detalle,$id_usuario);
    if($id!="-1")
    {$resultado=$id;
        try{
        $consulta="UPDATE usuario set id_casa=? where id=?";
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute(array($id,$id_usuario));
       
      }catch(PDOException $e)
      {
        $resultado=-1;
      }
    }
    return $resultado;
  }
 
  function insertar_trabajo($nombre,$detalle,$latitud,$longitud,$id_usuario)
  {$resultado=-1;
    $id=self::insertar_direccion($latitud,$longitud,$nombre,$detalle,$id_usuario);
    if($id!="-1")
    {$resultado=$id;
        try{
        $consulta="UPDATE usuario set id_trabajo=? where id=?";
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute(array($id,$id_usuario));
      }catch(PDOException $e)
      {
        $resultado=-1;
      }
    }
    return $resultado;
  }

 function get_direccion_por_id_empresa($id_empresa)
  {
    $consulta="SELECT * from direccion where nombre<>'' and estado=1 and id_empresa= ?";
  $resultado=-1;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_empresa));
      $row=$comando->fetchAll();
      if($row)
        { $resultado= $row;}

    }
    catch(PDOException $e)
    {
       $resultado=-1;
    }
    return $resultado;
  }


}
?>