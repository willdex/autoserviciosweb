<?php
require 'Basededatos.php';
class Tarifa extends Database
{
  public function Tarifa()
  {
    parent::Database();
  }
  function get_tarifa()
  {
   try{

		$consulta="SELECT * from tarifa";
		$comando=parent::getInstance()->getDb()->prepare($consulta);

		$comando->execute();
		$row=$comando->fetchAll();
 		return $row ;
		} catch (PDOException $e) {
		   return -1;
		}
  }
  function get_ultima_tarifa()
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
}
?>