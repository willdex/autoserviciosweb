<?php
include_once 'Basededatos.php';
include_once 'Push.php';
include_once 'Firebase.php';

class Usuario extends Database
{

	public function Usuario()
	{
 		parent::Database();
	}

	public static function perfil($celular)
	{
		//llama a un procedimiento almacenado
		$consulta = "call perfil_de_propietario(?)";

		try {
			//preparar sentencia
			$comando = parent::getInstance()->getDb()->prepare($consulta);
			//Ejecutar sentencia preparada
			$comando->execute(array($celular));
			//Capturar primera fila del resultado
			$row = $comando->fetch(PDO::FETCH_ASSOC);
			if($row)
			return $row;
			else
			return -1;
		} catch (PDOException $e) {
			//Aqui puedes clasificar el error dependiendo 
			//de la excepcion para presentarlo en la respuesta Json
			return -1;
		}
	}
	
	

	public static function get_perfil($celular)
	{
		// Creando consulta para el usuario
		$consulta = "SELECT id,nombre,apellido,telefono,email,id_empresa,token
		              FROM usuario WHERE telefono=?";
		try {
			//preparar sentencia
			$comando = parent::getInstance()->getDb()->prepare($consulta);
			//Ejecutar sentencia preparada
			$comando->execute(array($celular));
			//Capturar primera fila del resultado
			$row = $comando->fetch(PDO::FETCH_ASSOC);
			if($row)
			return $row;
			else
			return -1;
		} catch (PDOException $e) {
			//Aqui puedes clasificar el error dependiendo 
			//de la excepcion para presentarlo en la respuesta Json
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
	public static function get_perfil_por_id($id)
	{
		// Creando consulta para el usuario
		$consulta = "SELECT * FROM usuario WHERE id=?";
		try {
			//preparar sentencia
			$comando = parent::getInstance()->getDb()->prepare($consulta);
			//Ejecutar sentencia preparada
			$comando->execute(array($id));
			//Capturar primera fila del resultado
			$row = $comando->fetch(PDO::FETCH_ASSOC);
			if($row)
			return $row;
			else
			return -1;
		} catch (PDOException $e) {
			//Aqui puedes clasificar el error dependiendo 
			//de la excepcion para presentarlo en la respuesta Json
			return -1;
		}
	}
	
		public static function get_perfiles()
	{
		// Creando consulta para el usuario
		$consulta = "SELECT id,nombre,apellido,telefono,email,id_empresa
		              FROM usuario";

		try {
			//preparar sentencia
			$comando = parent::getInstance()->getDb()->prepare($consulta);

			//Capturar primera fila del resultado
			$row = $comando->fetchAll(PDO::FETCH_COLUMN);
			if($row)
			return $row;
			else
			return -1;
		} catch (PDOException $e) {
			//Aqui puedes clasificar el error dependiendo 
			//de la excepcion para presentarlo en la respuesta Json
			return -1;
		}		
	}

	public static function set_perfil($nombre,$apellido,$telefono,$email,$token)
	{
		try{
		$consulta="INSERT INTO usuario (nombre,apellido,telefono,email,token) values(:nombre,:apellido,:telefono,:email,:token)";
		
		$comando=parent::getInstance()->getDb()->prepare($consulta);
		$comando->bindParam(':nombre',$nombre);
		$comando->bindParam(':apellido',$apellido);
		$comando->bindParam(':telefono',$telefono);
		$comando->bindParam(':email',$email);
		$comando->bindParam(':token',$token);
 		$comando->execute();
 		return true;
		} catch (PDOException $e) {
		   return false;
		}
	}

	public static function existe_telefono($celular)
	{
		$consulta="SELECT * from usuario where telefono=?";
		try {
			//preparar sentencia
			$comando = parent::getInstance()->getDb()->prepare($consulta);
			//Ejecutar sentencia preparada
			$comando->execute(array($celular));
			$row = $comando->fetch(PDO::FETCH_ASSOC);
			if($row)
				{return true;}
			else
				{ return false;}

		} catch (PDOException $e) {
			return false;
		}
	}

	public static function existe_telefono_moto($celular)
	{
		$consulta="SELECT * from moto where celular=?";
		try {
			//preparar sentencia
			$comando = parent::getInstance()->getDb()->prepare($consulta);
			//Ejecutar sentencia preparada
			$comando->execute(array($celular));
			$row = $comando->fetch(PDO::FETCH_ASSOC);
			if($row)
				{return true;}
			else
				{ return false;}

		} catch (PDOException $e) {
			return false;
		}
	}

	public static function update_usuario($nombre,$apellido,$telefono,$email,$token)
	{
	try{
		$consulta="UPDATE usuario SET nombre=:nombre,apellido=:apellido,email=:email, token=:token where telefono= :telefono";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
		$comando->bindParam(':nombre',$nombre);
		$comando->bindParam(':apellido',$apellido);
		$comando->bindParam(':telefono',$telefono);
		$comando->bindParam(':email',$email);
		$comando->bindParam(':token',$token);
 		$comando->execute();
 		return true;
		} catch (PDOException $e) {
		   return false;
		}
	}

public static function modificar_nombre_apellido($id_usuario,$nombre,$apellido)
	{
	try{
		$consulta="UPDATE usuario SET nombre=?,apellido=? where id=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		 $comando->execute(array($nombre,$apellido,$id_usuario));
 		   return true;
		} catch (PDOException $e) {
		   return false;
		}
	}

public static function set_estado_activo_usuario($id_usuario)
	{
		try{
		$consulta="UPDATE usuario SET estado=1  where id=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		 $comando->execute(array($id_usuario));
 		   return true;
		} catch (PDOException $e) {
		   return false;
		}
	}
public static function set_estado_inactivo_usuario($id_usuario)
	{
	try{
		$consulta="UPDATE usuario SET estado=0  where id=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		 $comando->execute(array($id_usuario));
 		   return true;
		} catch (PDOException $e) {
		   return false;
		}
	}
public static function get_usuario_por_empresa($id_empresa)
	  {$consulta="SELECT * from usuario where estado=1 and id_empresa=?";
	  	try{
	  		    $comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_empresa));
				$row = $comando->fetchAll();
				if($row)
				{
				return $row;
				}
				else{
					return -1;
				}
	  	}catch(PDOException $e)
	  	{
	  		return -1;
	  	}
	  }

	  function get_usuario_sin_empresa()
	  {$consulta="select * from usuario where id_empresa is null";
	  	try{
	  		    $comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute();
				$row = $comando->fetchAll();
				if($row)
				{
				return $row;
				}
				else{
					return -1;
				}
	  	}catch(PDOException $e)
	  	{
	  		return -1;
	  	}
	  }
	  function insertar_casa($detalle,$latitud,$longitud,$id_usuario)
  {$resultado=false;
    $id=self::insertar_direccion($latitud,$longitud,$detalle,$id_usuario);
    if($id)
    {
        try{
        $consulta="UPDATE usuario set id_casa=? where id=?";
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

function existe_usuario_en_empresa($id_usuario)
	  {$consulta="select * from usuario where id=? and id_empresa is null";
	  	try{
	  		    $comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario));
				$row = $comando->fetchAll();
				if($row)
				{
				return true;
				}
				else{
					return false;
				}
	  	}catch(PDOException $e)
	  	{
	  		return false;
	  	}

	  }

function obtener_id_empresa_por_id_usuario($id_usuario)
	  {$consulta="select * from usuario where id=? and id_empresa is not null";
	    $resultado=-1;
	  	try{
	  		    $comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario));
				$row = $comando->fetch(PDO::FETCH_ASSOC);
				if($row)
				{
				 $resultado=$row['id_empresa'];
				}
				else{
					 $resultado=-1;
				}
	  	}catch(PDOException $e)
	  	{
	  		 $resultado=-1;
	  	}
	  	return $resultado;

	  }

	  function insertar_usuario_a_empresa($id_usuario,$id_empresa)
  {$resultado=false;
        try{ 
        	if(self::existe_usuario_en_empresa($id_usuario)==true)
        	{
		        $consulta="UPDATE usuario set id_empresa=".$id_empresa." where id=".$id_usuario;
		        $comando=parent::getInstance()->getDb()->prepare($consulta);
		        $comando->execute();
		        $usuario=self::get_perfil_por_id($id_usuario);
		        if($usuario!="-1")
			        {	$token=self::get_token_por_id_usuario($id_usuario);
						$sw=self::enviar_notificacion_usuario($token,$usuario);
						$resultado=true;
			        }
			        else
			        {
			        	$resultado=false;
			        }		        
    		}else
    		{
    			$resultado=false;
    		}
      }catch(PDOException $e)
      {
        $resultado=false;
      }
      
      return  $resultado;
   
  }

  	function enviar_notificacion_usuario($token,$usuario)
	  {try{
		   $push = new Push('Asapp',$usuario['nombre'].' a sido agregado a la empresa ',null,"usuario","","","","","7");
	     // obteniendo el empuje del objeto push
		   $push->set_id_empresa($usuario['id_empresa']); 
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


  function insertar_oficina($detalle,$latitud,$longitud,$id_usuario)
  {$resultado=false;
    $id=self::insertar_direccion($latitud,$longitud,$detalle,$id_usuario);
    if($id)
    {
        try{
        $consulta="UPDATE usuario set id_oficina=? where id=?";
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
        $consulta="UPDATE usuario set id_trabajo=? where id=?";
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
  function get_casa($id_usuario)
  {
  	$resultado=-1;
  	$consulta=" select d.* from direccion d, usuario u where d.id_usuario=u.id and u.id_casa=d.id  and u.id= ?";
  	try{
  	$comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_usuario));
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
  function get_trabajo($id_usuario)
  {
  	$resultado=-1;
  	$consulta=" select d.* from direccion d, usuario u where d.id_usuario=u.id and u.id_trabajo=d.id  and u.id= ?";
  	try{
  	$comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_usuario));
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
  function get_oficina($id_usuario)
  {
  	$resultado=-1;
  	$consulta=" select d.* from direccion d, usuario u where d.id_usuario=u.id and u.id_oficina=d.id  and u.id= ?";
  	try{
  	$comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_usuario));
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

   function get_token_moto()
   {   $query = parent::getInstance()->getDb()->prepare("SELECT token FROM moto");
        $query->execute(); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
 array_push($tokens, $row->token);
    }
        return $tokens; 
   }
    function get_token_por_id_usuario($id_usuario)
   {   $query = parent::getInstance()->getDb()->prepare("SELECT token FROM usuario where id= ? ");
        $query->execute( array($id_usuario)); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
 array_push($tokens, $row->token);
    }
        return $tokens; 
   }
  function pedir_moto($id_usuario,$latitud,$longitud)
  {try{
	   $push = new Push('Pedir Moto','',null);
     // obteniendo el empuje del objeto push
		 $mPushNotification = $push->getPush(); 
		 
		 // obtener el token del objeto de base de datos

		 $devicetoken = self::get_token_moto();		 

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


  public static function insertar_imagen($id_usuario,$imagen)
	{
		
		//llama a un procedimiento almacenado
		$consulta = "UPDATE usuario set imagen= ? where id=?";

		try {
			//preparar sentencia
			$comando = parent::getInstance()->getDb()->prepare($consulta);
			//Ejecutar sentencia preparada
			$comando->execute(array($imagen,$id_usuario));
			//Capturar primera fila del resultado
			return true;
			
		} catch (PDOException $e) {
			return false;
		}
	} 

public static function insertar_usuario($nombre,$apellido,$telefono,$email,$token,$imagen)
	{try{
		$consulta="INSERT INTO usuario (nombre,apellido,telefono,email,token,imagen) values('$nombre','$apellido','$telefono','$email','$token','$imagen')";

		
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute();
 		$lastId = parent::getInstance()->getDb()->lastInsertId();
 		return $lastId;
		} catch (PDOException $e) {
		   return "-1";
		}
	} 

public static function insertar_usuario_por_administrador($nombre,$telefono,$id_empresa)
	{try{
		$consulta="INSERT INTO usuario (nombre,telefono,id_empresa) values('$nombre','$telefono','$id_empresa')";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute();
 		$lastId = parent::getInstance()->getDb()->lastInsertId();
 		return $lastId;
		} catch (PDOException $e) {
		   return "-1";
		}
	} 
 function existe_usuario_por_telefono($telefono)
  {
  	$resultado=-1;
  	$consulta=" select id from usuario where telefono=?";
  	try{
  	$comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($telefono));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
    if($row)
    {
    	$resultado=$row['id'];
    }

	}catch(PDOException $e)
	{
		$resultado=-1;
	}
  	return $resultado;
  }

public static	function cargar_token($telefono,$token)
   {
    $consulta="UPDATE usuario set token =? where telefono=? ";
      $aceptado=false;
			try{
  			    $comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($token,$telefono));
				$aceptado=true;
	  		}catch(PDOException $e)
	  		{
	  			$aceptado=false;
	  		}
	  	

return $aceptado;
   }



  public static  function get_configuracion()
  {$consulta="SELECT * from configuracion limit 1";
  	try{
  		$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute();
		  $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return array($row);
      }
      else
			return -1;
  	}catch(PDOException $e)
  	{
  		return -1;
  	}
  }
 public static  function get_empresa($id_usuario)
  {$consulta="SELECT e.* from empresa e,usuario u where u.id_empresa=e.id and u.id=?  limit 1";
  	try{
  		$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_usuario));
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