<?php
require('Basededatos.php');
class Configuracion extends Database
{
  public function Configuracion()
  {
    parent::Database();
  }

  function get_configuracion()
  {$consulta="SELECT * from configuracion limit 1";
  	try{
  		$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute();
		  $row=$comando->fetch(PDO::FETCH_ASSOC);
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
 

}
?>