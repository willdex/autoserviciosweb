<?php
require('Basededatos.php');
class Empresa extends Database
{
  public function Empresa()
  {
    parent::Database();
  }
  function set_empresa($detalle,$latitud,$longitud,$id_empresa,$id_usuario)
  {
   try{

		$consulta="INSERT INTO empresa (nombre,direccion,telefono,razon_social,nit,latitud,longitud) values(?,?,?,?,?,?,?)";
		$comando=parent::getInstance()->getDb()->prepare($consulta);

		$comando->execute(array($nombre,$direccion,$telefono,$razon_social,$nit,$latitud,$longitud));
 		return true;
		} catch (PDOException $e) {
		   return false;
		}
  }
  function get_empresa_por_id_usuario($id_usuario)
  {$consulta="select e.* from empresa e, usuario u where e.id=u.id_empresa and u.id=?";
  	try{
  		    $comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_usuario));
			$row = $comando->fetchAll();
			return $row;
  	}catch(PDOException $e)
  	{
  		return -1;
  	}
  }
  function get_administrador($id_usuario)
  {$resultado=-1;
    $consulta="select * from empresa where id_administrador=?";
    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_usuario));
      $row = $comando->fetchAll();
      if($row)
        {$resultado=$row;}
      
    }catch(PDOException $e)
    {
      $resultado=-1;
    }
    return $resultado;
  }

function  actualizar_datos($id_empresa,$id_administrador,$direccion,$telefono,$razon_social)
  {
    $consulta="update empresa set direccion=?, telefono=?, razon_social= ?  where id_administrador=? and id=?";
    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($direccion,$telefono,$razon_social,$id_administrador,$id_empresa));
      return true;
    }catch(PDOException $e)
    {
     return false;
    }
  }

}
?>