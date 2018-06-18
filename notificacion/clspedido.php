<?php
include_once 'Basededatos.php';
include_once 'Push.php';
include_once 'Firebase.php';

class Pedido extends Database
{
public function Pedido()
	{	
	  parent::Database();	
	}	

	public static function get_pedidos_por_id_motista($id_motista)
	{
		$consulta="SELECT p.*, concat(nombre,' ',apellido)as 'nombre_usuario',(select sum(monto) from carrera ca where ca.id_pedido=p.id)as 'monto' from pedido p,usuario u where p.id_usuario=u.id and p.id_moto=? order by p.fecha desc";
		try {
			$comando = parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_motista));
			$row=$comando->fetchAll();
			return $row;
		} catch (PDOException $e) {
			return -1;
		}
	}
	public static function get_pedido_por_celular_usuario($celular)
	{
		$consulta="SELECT concat(m.nombre,' ',m.apellido) as 'nombre_moto',m.celular,m.id as 'id_moto',m.marca,m.placa,p.id as 'id_pedido',u.id as 'id_usuario' from pedido p,moto m,usuario u where m.id=p.id_moto and p.id_usuario=u.id AND p.estado<2 and u.telefono=?";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($celular));
			$row=$comando->fetch(PDO::FETCH_ASSOC);
			return $row;
		}catch(PDOException $e)
		{
			return -1;
		}
	}
	public static function get_pedido_por_id_pedido($id_pedido)
	{
		$consulta="SELECT concat(m.nombre,' ',m.apellido) as 'nombre_moto',m.celular,m.id as 'id_moto',m.marca,m.placa,p.id as 'id_pedido',u.id as 'id_usuario',m.color,p.latitud,p.longitud from pedido p,moto m,usuario u where m.id=p.id_moto and p.id_usuario=u.id and p.estado<2 and p.id=?";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_pedido));
			$row=$comando->fetch(PDO::FETCH_ASSOC);
			if($row)
				{return $row;}
			else
			{
				return -1;
			}
			
		}catch(PDOException $e)
		{
			return -1;
		}
	}
	public static function get_pedido_por_id_usuario($id_usuario)
	{
		$consulta="SELECT concat(m.nombre,' ',m.apellido) as 'nombre_moto',m.celular,m.id as 'id_moto',m.marca,m.placa,p.id as 'id_pedido',u.id as 'id_usuario',m.color,p.latitud,p.longitud,p.estado from pedido p,moto m,usuario u where m.id=p.id_moto and p.id_usuario=u.id and p.estado<2 and p.id_usuario=? order by id_pedido desc limit 1";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_usuario));
			$row=$comando->fetch(PDO::FETCH_ASSOC);
			if($row)
				{return $row;}
			else
			{
				return -1;
			}
			
		}catch(PDOException $e)
		{
			return -1;
		}
	}



	public static function get_pedido_por_id_moto($id_moto)
	{
		$consulta="SELECT p.*, concat(u.nombre,' ',u.apellido)as 'nombre_usuario',e.nombre as 'empresa',d.nombre as 'nombre_direccion',d.detalle as 'detalle_direccion' from pedido p,usuario u ,empresa e,direccion d where e.id=d.id_empresa and d.latitud=p.latitud and d.longitud=p.longitud and u.id_empresa=e.id and p.id_usuario=u.id and p.estado<2 and p.id_moto=? order by p.id desc limit 1 ";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_moto));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
	}

	public static function pedido_no_aceptado($id_usuario)
	{//consulta para verificar si el usuario tiene un pedido que ningun motista a aceptado.
		$resultado=-1;
		$consulta="SELECT  * from pedido where  estado=0 and id_moto is null and TIMESTAMPDIFF(MINUTE,fecha,now())>2 and id_usuario=? limit 1";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_usuario));
			$row=$comando->fetch(PDO::FETCH_ASSOC);
			if($row)
				{$resultado=$row['id'];}
			else
			{
				$resultado=-1;
			}
			
		}catch(PDOException $e)
		{
			$resultado=-1;
		}
		return $resultado;
	}

	public static function get_direccion_por_latitud_longitud($latitud,$longitud,$id_usuario)
	  {
	    $consulta="SELECT d.* from direccion d,usuario u where d.id_empresa=u.id_empresa and d.latitud= ? and d.longitud=? and u.id=? limit 1";
	    try{
	       $comando=parent::getInstance()->getDb()->prepare($consulta);
	      $comando->execute(array($latitud,$longitud,$id_usuario));
	      $row = $comando->fetch(PDO::FETCH_ASSOC);
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

	    public static function iniciar_carrera($direccion_inicio,$opcion,$id_pedido,$id_usuario,$id_moto)
  {
   try{

		$consulta="INSERT INTO carrera (direccion_inicio,opciones,id_pedido,id_usuario,id_moto) values(?,?,?,?,?)";
		$comando=parent::getInstance()->getDb()->prepare($consulta);

		$comando->execute(array($direccion_inicio,$opcion,$id_pedido,$id_usuario,$id_moto));
 		return true;
		} catch (PDOException $e) {
		   return false;
		}
  }

   public  static function set_direccion($detalle,$latitud,$longitud,$id_empresa,$id_usuario)
  {

   try{

    if($id_empresa!="")
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

public static function llego_la_moto($id_pedido)
	{ $resultado=false;
		#Confirmamos el pedido.  diciendo que el motista llego,
		try{
			$confirmar_pedido="UPDATE pedido set estado='1' , fecha_llegado = now() where id= ?";
			$comando=parent::getInstance()->getDb()->prepare($confirmar_pedido);
			$comando->execute(array($id_pedido));
			$resultado=true;
            }
            catch(PDOException $e)
            {

            }		
            return $resultado;
     }

     public static function lista_pedido_por_id_usuario($id_usuario)
		{
			$res="-1";
			
		try{
			$consulta="SELECT p.*,CONCAT(HOUR(p.fecha),':',MINUTE(p.fecha),':',SECOND(p.fecha))as 'hora',m.nombre,m.apellido,m.celular,m.marca,m.placa,m.estado as 'estado_moto',(select d.nombre from direccion d,usuario usu where d.latitud=p.latitud and d.longitud=p.longitud and d.id_empresa=usu.id_empresa and usu.id=p.id_usuario limit 1)as 'nombre_direccion',(select d.detalle from direccion d,usuario usu where d.latitud=p.latitud and d.longitud=p.longitud and d.id_empresa=usu.id_empresa and usu.id=p.id_usuario limit 1)as 'detalle_direccion',(select sum(monto) from carrera ca where ca.id_pedido=p.id)as 'monto_total' from pedido p,moto m where EXISTS(select * from carrera ca where ca.id_pedido=p.id) and p.id_moto=m.id and p.estado=2 and p.id_usuario=? order by p.id desc limit 50";
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario));
				$row=$comando->fetchAll();
				if($row)
					{$res=$row;}
					 else
					 {$res="-1";}
			}catch(PDOException $e)
			{
			    $res="-1";
			}
			return $res;
		}


 public static function lista_pedido_por_id_empresa_por_fecha($id_empresa,$dia,$mes,$anio)
		{
			$res="-1";
			
		try{
			$consulta="SELECT p.*,CONCAT(HOUR(p.fecha),':',MINUTE(p.fecha),':',SECOND(p.fecha))as 'hora',CONCAT(u.nombre,u.apellido)as 'nombre_usuario',m.nombre,m.apellido,m.celular,m.marca,m.placa,m.estado as 'estado_moto',(select d.nombre from direccion d,usuario usu where d.latitud=p.latitud and d.longitud=p.longitud and d.id_empresa=usu.id_empresa and usu.id=p.id_usuario limit 1)as 'nombre_direccion',(select d.detalle from direccion d,usuario usu where d.latitud=p.latitud and d.longitud=p.longitud and d.id_empresa=usu.id_empresa and usu.id=p.id_usuario limit 1)as 'detalle_direccion',(select sum(monto) from carrera ca where ca.id_pedido=p.id)as 'monto_total' from pedido p,moto m,usuario u where EXISTS(select * from carrera ca where ca.id_pedido=p.id) and p.id_moto=m.id and p.estado=2 and p.id_usuario=u.id and u.id_empresa=? and DAY(p.fecha)=? and MONTH(p.fecha)=? and YEAR(p.fecha)=? order by p.id desc limit 50";
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_empresa,$dia,$mes,$anio));
				$row=$comando->fetchAll();
				if($row)
					{$res=$row;}
					 else
					 {$res="-1";}
			}catch(PDOException $e)
			{
			    $res="-1";
			}
			return $res;
		}
 
 public static function lista_pedido_por_id_usuario_por_fecha($id_usuario,$dia,$mes,$anio)
		{
			$res="-1";
			
		try{
			$consulta="SELECT p.*,CONCAT(HOUR(p.fecha),':',MINUTE(p.fecha),':',SECOND(p.fecha))as 'hora',m.nombre,m.apellido,m.celular,m.marca,m.placa,m.estado as 'estado_moto',(select d.nombre from direccion d,usuario usu where d.latitud=p.latitud and d.longitud=p.longitud and d.id_empresa=usu.id_empresa and usu.id=p.id_usuario limit 1)as 'nombre_direccion',(select d.detalle from direccion d,usuario usu where d.latitud=p.latitud and d.longitud=p.longitud and d.id_empresa=usu.id_empresa and usu.id=p.id_usuario limit 1)as 'detalle_direccion',(select sum(monto) from carrera ca where ca.id_pedido=p.id)as 'monto_total' from pedido p,moto m where EXISTS(select * from carrera ca where ca.id_pedido=p.id) and p.id_moto=m.id and p.estado=2 and p.id_usuario=? and DAY(p.fecha)=? and MONTH(p.fecha)=? and YEAR(p.fecha)= ? order by p.id desc limit 50";
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario,$dia,$mes,$anio));
				$row=$comando->fetchAll();
				if($row)
					{$res=$row;}
					 else
					 {$res="-1";}
			}catch(PDOException $e)
			{
			    $res="-1";
			}
			return $res;
		}
 public static function lista_pedido_por_id_moto_por_fecha($id_moto,$dia,$mes,$anio)
		{
			$res="-1";
			
		try{
			$consulta="SELECT p.*,CONCAT(HOUR(p.fecha),':',MINUTE(p.fecha),':',SECOND(p.fecha))as 'hora',e.nombre as 'empresa',CONCAT(u.nombre,u.apellido)as 'nombre_usuario',(select d.nombre from direccion d,usuario usu where d.latitud=p.latitud and d.longitud=p.longitud and d.id_empresa=usu.id_empresa and usu.id=p.id_usuario limit 1)as 'nombre_direccion',(select d.detalle from direccion d,usuario usu where d.latitud=p.latitud and d.longitud=p.longitud and d.id_empresa=usu.id_empresa and usu.id=p.id_usuario limit 1)as 'detalle_direccion',(select sum(monto) from carrera ca where ca.id_pedido=p.id)as 'monto_total' from pedido p,usuario u,empresa e where EXISTS(select * from carrera ca where ca.id_pedido=p.id) and p.estado=2 and p.id_usuario=u.id and p.id_moto=? and DAY(p.fecha)=? and MONTH(p.fecha)=? and YEAR(p.fecha)=? and e.id=u.id_empresa order by p.id desc limit 50";
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_moto,$dia,$mes,$anio));
				$row=$comando->fetchAll();
				if($row)
					{$res=$row;}
					 else
					 {$res="-1";}
			}catch(PDOException $e)
			{
			    $res="-1";
			}
			return $res;
		}
	public static function pedido_en_curso($id_moto)
	{
		$consulta="SELECT p.*, concat(nombre,' ',apellido)as 'nombre_usuario' from pedido p,usuario u where p.id_usuario=u.id and p.id_moto=? and p.id_usuario=u.id  and p.estado<2";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_moto));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
	}

	function get_carrera_en_curso_por_id($id_pedido)
  {$resultado=-1;
    $consulta ="select c.id,c.direccion_inicio,d.latitud as 'latitud_inicio',d.longitud as 'longitud_inicio',c.direccion_fin,c.distancia,c.opciones,c.fecha_inicio as 'fecha1',c.fecha_fin as 'fecha2',c.id_pedido,c.id_usuario,c.id_moto,c.id_tarifa,c.monto from carrera c, direccion d where d.id=c.direccion_inicio and  c.id_pedido= ? order by c.id desc limit 1";
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

	public static function pedido_en_curso_por_id($id_pedido)
	{
		$consulta="SELECT p.*, concat(u.nombre,' ',u.apellido)as 'nombre_usuario',e.nombre as 'empresa',d.nombre as 'nombre_direccion',d.detalle as 'detalle_direccion' from pedido p,usuario u ,empresa e,direccion d where e.id=d.id_empresa and d.latitud=p.latitud and d.longitud=p.longitud and u.id_empresa=e.id and p.id_usuario=u.id and p.id=? limit 1";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_pedido));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
					return array($row);
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
	}
	public static function get_estado_pedido($id_pedido)
	{
		$consulta="SELECT estado  from pedido where id=?";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_pedido));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
	}

	public static function terminar_todo_pedido($id_pedido)
	{
		$consulta="UPDATE pedido SET estado='2' where id=?";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_pedido));
				self::notificacion_terminar_todo_pedido($id_pedido);
				return true;
				
			}catch(PDOException $e)
			{
			     return false;
			}
	}
	public static function cancelar_pedido($id_pedido,$distancia)
	{
		$consulta="UPDATE pedido SET estado='3',distancia=? where id=?";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($distancia,$id_pedido));
				self::notificacion_cancelar_pedido($id_pedido);
				return true;
				
			}catch(PDOException $e)
			{
			     return false;
			}
	}
	public static function pedir_moto($id_usuario,$latitud,$longitud,$mensaje,$nombre,$id_empresa)
	{
  		$resultado=-1;
  	
	  	$id_pedido=self::id_ultimo_pedido($id_usuario);

	  	if($id_pedido=='-1')
	  	{
			$consulta="INSERT INTO pedido (id_usuario,latitud,longitud,mensaje) values(?,?,?,?)";
			try{
  			$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario,$latitud,$longitud,$mensaje));
				$lastId = parent::getInstance()->getDb()->lastInsertId();
          		$resultado=$lastId;


				$id_direccion=self::insertar_direccion($latitud,$longitud,'',$id_usuario,$id_empresa);

          		$no=self::enviar_notificacion_pedido_moto($id_usuario,$latitud,$longitud,$lastId,$nombre);
	  		}catch(PDOException $e)
	  		{
	  			$resultado=-1;
	  		}
	  	}
	  	else
	  	{
			$consulta="UPDATE pedido set id_usuario=?,latitud=?,longitud=?,mensaje=?,fecha=now() where id=?";
			try{
  			$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario,$latitud,$longitud,$mensaje,$id_pedido));
				$resultado=$id_pedido;
				$id_direccion=self::insertar_direccion($latitud,$longitud,'Ubicacion del Administrador',$id_usuario,$id_empresa);
          		$no=self::enviar_notificacion_pedido_moto($id_usuario,$latitud,$longitud,$id_pedido,$nombre);
	  		}catch(PDOException $e)
	  		{
	  			$resultado=-1;
	  		}	
	  	}

	  	return $resultado;
	}
	public static function enviar_notificacion_pedido_moto($id_usuario,$latitud,$longitud,$id_pedido,$nombre)
	  {try{
		   $push = new Push('Pedido',$nombre.' a pedido una moto.',null,"moto",$id_pedido,$nombre,$latitud,$longitud,"2");
	     // obteniendo el empuje del objeto push
		   $push->set_empresa(self::get_empresa_por_id_usuario($id_usuario));
			 $direccion=self::get_direccion_por_latitud_longitud($latitud,$longitud,$id_usuario);
			 if($direccion!=-1)
			 {
			 	$push->set_nombre_direccion($direccion['nombre']);
			 	$push->set_detalle_direccion($direccion['detalle']);
			 }
			 $mPushNotification = $push->getPush(); 
			 // obtener el token del objeto de base de datos

			 $devicetoken = self::get_token_moto($latitud,$longitud);		 

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

	public static function get_token_moto($latitud,$longitud)
   {   $query = parent::getInstance()->getDb()->prepare("SELECT token,distancia_entre_dos_puntos(?,?,latitud,longitud) as distancia FROM moto where  estado=1 and credito>0 and login=1 and distancia_entre_dos_puntos(?,?,latitud,longitud)<= ? order by distancia asc");
         $query->execute(array($latitud, $longitud,$latitud, $longitud,8000)); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
		 array_push($tokens, $row->token);
		    }
        return $tokens; 
   }

// registro del ultimo pedido... 
  public static function id_ultimo_pedido($id_usuario)
   {
   	try{
   	$query = parent::getInstance()->getDb()->prepare("select * from pedido where date(fecha)=date(now()) and id_usuario=? and estado<=1 limit 1");
        $query->execute(array($id_usuario)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row['id'];
			}
			else
			{
				return -1;
			}
		}
		catch(PDOException $e)
		{	
        return -1; 
    }
   }
    public static function get_estado_usuario($id_usuario)
   {
   	try{
   	$query = parent::getInstance()->getDb()->prepare("SELECT estado from usuario where id=?");
        $query->execute(array($id_usuario)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row['estado'];
			}
			else
			{
				return -1;
			}
		}
		catch(PDOException $e)
		{	
        return -1; 
    }
   }

    public static function get_nombre_por_id_usuario($id_usuario)
   {//obtener el  nombre de lla empresa y enviale el nombre.
   	try{
   	$query = parent::getInstance()->getDb()->prepare("select e.* from empresa e,usuario u where u.id=? and u.id_empresa=e.id limit 1");
        $query->execute(array($id_usuario)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row;
			}
			else
			{
				return -1;
			}
		}
		catch(PDOException $e)
		{	
        return -1; 
    }
   }
    public static function get_empresa_por_id_usuario($id_usuario)
   {//obtener el  nombre de lla empresa y enviale el nombre.
   	try{
   	$query = parent::getInstance()->getDb()->prepare("select e.* from empresa e,usuario u where u.id=? and u.id_empresa=e.id limit 1");
        $query->execute(array($id_usuario)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return array($row);
			}
			else
			{
				return -1;
			}
		}
		catch(PDOException $e)
		{	
        return -1; 
    }
   }


    public static function id_ultimo_pedido_en_proceso($id_usuario)
   {
   	try{
   	$query = parent::getInstance()->getDb()->prepare("select * from pedido where date(fecha)=date(now()) and id_usuario=? and estado=0 and id_moto is not null limit 1");
        $query->execute(array($id_usuario)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row['id'];
			}
			else
			{
				return -1;
			}
		}
		catch(PDOException $e)
		{	
        return -1; 
    }
   }


  public static function get_id_ultimo_pedido_por_id_usuario($id_usuario)
   {
	try{
   	$query = parent::getInstance()->getDb()->prepare("SELECT id from pedido where estado<2 and id_usuario=? and id_moto is not null limit 1");
        $query->execute(array($id_usuario)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row['id'];
			}
			else
			{
				return -1;
			}
		}
		catch(PDOException $e)
		{	
        return -1; 
    }
   }
   public static function get_moto($id_moto)
  { $consulta="SELECT * from moto where id_moto= ?";
  $resultado=-1;
    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_moto));
      $row = $comando->fetch(PDO::FETCH_ASSOC);
           if($row)
           {
           	 $resultado=$row; 
           }
          else
          {
          	 $resultado=-1; 
          }

    }catch(PDOException $e)
    {
      $resultado=-1;
    }
    return $resultado;
  }
public static function resta_credito($cantidad,$id_moto)
{
$consulta="UPDATE pedido SET credito=credito-".$cantidad." where id=".$id_moto;
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute();
				return true;				
			}catch(PDOException $e)
			{
			     return false;
			}
}
  public static function aceptar_pedido($id_pedido,$id_moto)
   { //esta funcion registra el id de la moto en el pedido que acaba de aceptar.......y si el pedio ya a sido registrado entonces devuelve que no se puede registrar...
   	$res=false;

   	  if(self::id_moto_del_pedido($id_pedido)!=$id_moto){
    $consulta="UPDATE pedido set id_moto=?, estado=0 where id=? and TIMESTAMPDIFF(MINUTE,fecha,now())<=2 and id_moto is null";
      $aceptado=false;
			try{
  			    $comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_moto,$id_pedido));
				$aceptado=true;
	  		}catch(PDOException $e)
	  		{
	  			$aceptado=false;
	  		}
	  		//si no hubo ningun problema al actualizar entonces verificamos si el id_moto esta en el pedido
	  		if($aceptado==true)
	  		{
	  			$resultado=self::id_moto_del_pedido($id_pedido);
	  			if($resultado==$id_moto)
	  			{
	  				$res=true;	
	  				$moto=self::resta_credito("1",$id_moto);
	
	  				$token=self::get_token_id_pedido($id_pedido);

	  				$nombre_completo=self::nombre_completo_del_motista_por_id($id_moto);
	  				self::enviar_notificacion_aceptar_pedido($token,$id_pedido,$nombre_completo);
	  			}
	  			
	  		}
		}

return $res;
   }


//notificaciones......
    	public static function enviar_notificacion_aceptar_pedido($token,$id_pedido,$nombre)
	  {try{
		   $push = new Push('Pedido',$nombre.' acepto su pedido.',null,"usuario",$id_pedido,"","","","1");
	     // obteniendo el empuje del objeto push
				$pedido=self::get_pedido_por_id_pedido($id_pedido);
				$push->set_pedido(array($pedido)); 
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


public static function estoy_cerca($id_pedido)
   { //esta funcion registra el id de la moto en el pedido que acaba de aceptar.......y si el pedio ya a sido registrado entonces devuelve que no se puede registrar...
   	$res=false;
  

	  	
	  try{
	  	$token=self::get_token_id_pedido($id_pedido);
	  	//verificar el id del motista.,.
	  	$id_moto=self::id_moto_del_pedido($id_pedido);
	  	if($id_moto!=-1)
	  	{
	  	  $nombre_completo=self::nombre_completo_del_motista_por_id($id_moto);
	  	}
	  	else
	  	{
	  		$nombre_completo="Su Moto";
	  	}

		   $push = new Push('Pedido',$nombre_completo.' esta llegando.',null,"usuario",$id_pedido,"","","","10");
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


public static function notificacion_llego_la_moto($id_pedido)
   { //esta funcion registra el id de la moto en el pedido que acaba de aceptar.......y si el pedio ya a sido registrado entonces devuelve que no se puede registrar...
   	$res=false;
  

	  	
	  try{
	  	$token=self::get_token_id_pedido($id_pedido);

	  	//verificar el id del motista.,.
	  	$id_moto=self::id_moto_del_pedido($id_pedido);
	  	if($id_moto!=-1)
	  	{
	  	  $nombre_completo=self::nombre_completo_del_motista_por_id($id_moto);
	  	}
	  	else
	  	{
	  		$nombre_completo="Su Moto";
	  	}

	  				
		   $push = new Push('Pedido',$nombre_completo.' ha llegado.',null,"usuario",$id_pedido,"","","","11");
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
   public static function notificacion_cancelar_pedido($id_pedido)
   { //esta funcion registra el id de la moto en el pedido que acaba de aceptar.......y si el pedio ya a sido registrado entonces devuelve que no se puede registrar...
   	$res=false;

	  try{
	  	$token=self::get_token_moto_id_pedido($id_pedido);
	  				
		   $push = new Push('Pedido','El pedido se Cancelo.',null,"moto",$id_pedido,"","","","9");
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


public static function notificacion_no_hay_moto_disponible($id_pedido)
   { //notificaicon que envia al usuario.. con un mensaje de que ninguna moto acepto su pedido..
   	$res=false;

	  try{
	  	$token=self::get_token_moto_id_pedido($id_pedido);
	  				
		   $push = new Push('Pedido','No hay Moto disponible.',null,"usuario",$id_pedido,"","","","13");
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

public static function notificacion_terminar_todo_pedido($id_pedido)
   { //esta funcion registra el id de la moto en el pedido que acaba de aceptar.......y si el pedio ya a sido registrado entonces devuelve que no se puede registrar...
   	$res=false;
  

	  	
	  try{
	  	$token=self::get_token_id_pedido($id_pedido);
	  				
		   $push = new Push('Pedido','El pedido se ha finalizo con exito.',null,"usuario",$id_pedido,"","","","5");
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







//OBTENER TOKEN.....
   public static function get_token_id_pedido($id_pedido)
   { $query = parent::getInstance()->getDb()->prepare("SELECT u.token from usuario u, pedido p where u.id=p.id_usuario and p.id=?");
        $query->execute(array($id_pedido)); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
 			array_push($tokens, $row->token);
    }
        return $tokens; 
   }
    public static function get_token_moto_id_pedido($id_pedido)
   { $query = parent::getInstance()->getDb()->prepare("SELECT m.token from moto m, pedido p where m.id=p.id_moto and p.id=?");
        $query->execute(array($id_pedido)); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
 			array_push($tokens, $row->token);
    }
        return $tokens; 
   }
  public static function id_moto_del_pedido($id_pedido)
   {$resultado=-1;
   	try{
   	$query = parent::getInstance()->getDb()->prepare("select id_moto from pedido where id=?");
        $query->execute(array($id_pedido)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				$resultado=$row['id_moto'];
			}
			else
			{
				$resultado=-1;
			}
		}
		catch(PDOException $e)
		{	
        $resultado =-1; 
    }
    return $resultado;
   }
    public static function nombre_completo_del_motista_por_id($id_moto)
   {$resultado=-1;
   	try{
   	$query = parent::getInstance()->getDb()->prepare("select concat(nombre,' ',apellido)as 'nombre' from moto where id=?");
        $query->execute(array($id_moto)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				$resultado=$row['nombre'];
			}
			else
			{
				$resultado=-1;
			}
		}
		catch(PDOException $e)
		{	
        $resultado =-1; 
    }
    return $resultado;
   }
   public function cargar_credito($id_moto,$credito)
   {
   		
   }

  public static function puntos_de_carreras_por_id_pedido($id_pedido)
   {
   	$rutas;
   	 $consulta = parent::getInstance()->getDb()->prepare("SELECT id_carrera FROM pedido where id= ?");
        $consulta->execute(array($id_pedido)); 
         $tokens = array(); 
        while($row=$consulta->fetch(PDO::FETCH_OBJ)) {
 		array_push($tokens, $row->id_carrera);
 		   $carrera = parent::getInstance()->getDb()->prepare("SELECT * FROM ruta where id_pedido= ? and id_carrera=?");
       		$carrera->execute(array($id_pedido,$row->id_carrera)); 
       		$ruta="";
       		 while($car=$carrera->fetch(PDO::FETCH_OBJ))
       		 {
       		 	$ruta=$ruta."|".$car->latitud.",".$car->longitud;
       		 }
       		 $rutas['carrera']="https://maps.googleapis.com/maps/api/staticmap?"."path=color:0x0000ff|weight:5".$ruta;
       
    }
       json_encode($rutas); 
   	
   }

    function insertar_direccion($latitud,$longitud,$detalle,$id_usuario,$id_empresa)
  { 
  	
     $direccion=self::existe_direccion($latitud,$longitud,$id_usuario,$id_empresa);

     if($direccion=="-1")
     {
       $consulta="INSERT INTO direccion (nombre,detalle,latitud,longitud,id_usuario,id_empresa) values('',?,?,?,?,?)";
       
        try{
              $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($detalle,$latitud,$longitud,$id_usuario,$id_empresa));
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

   function existe_direccion($latitud,$longitud,$id_usuario,$id_empresa)
  {
     $consulta="SELECT * from direccion where latitud=? and longitud=? and id_usuario=? and id_empresa=?";
  $resultado=-1;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($latitud,$longitud,$id_usuario,$id_empresa));
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


   public static function moto_disponible($latitud,$longitud)
   {  

   	$query = parent::getInstance()->getDb()->prepare("SELECT token,distancia_entre_dos_puntos(?,?,latitud,longitud) as distancia FROM moto where  estado=1 and credito>0 and login=1 and distancia_entre_dos_puntos(?,?,latitud,longitud)<= ? order by distancia asc ");
        $query->execute(array($latitud, $longitud,$latitud, $longitud,8000)); 
		$row=$query->fetchAll();
		if($row)
		{
			return true;
		}
		else
			{
				return false;
			}
   }


     public static function get_estado_empresa_por_id_usuario($id_usuario)
   {
   	try{
   	$query = parent::getInstance()->getDb()->prepare("SELECT  e.estado as 'estado' from empresa e, usuario u where e.id=u.id_empresa and u.id=? limit 1 ");
        $query->execute(array($id_usuario)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row['estado'];
			}
			else
			{
				return -1;
			}
		}
		catch(PDOException $e)
		{	
        return -1; 
    }
   }

   public static function set_puntuacion($id_pedido,$puntuacion)
	{
		$consulta="UPDATE pedido SET puntuacion=? where id=?";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($puntuacion,$id_pedido));
				return true;
				
			}catch(PDOException $e)
			{
			     return false;
			}
	}

  
}




?>