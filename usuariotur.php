<?php 

/**
* Representa la estructura del usuario
* ya sea chofer, alquilante o socio 
* almacenadas en la abase de datos 
*/
require 'Basededatos.php';


class usuarios
{

	function __construct()
	{}

	/**
	 * Obtiene los campos de un usuario como 
	 * nombre(nro)
	 * @param $nro numero de movil
	 * @return mixed 
	 */
	public static function perfil($nro)
	{
		//llama a un procedimiento almacenado
		$consulta = "call perfil_de_propietario(?)";

		try {
			//preparar sentencia
			$comando = Database::getInstance()->getDb()->prepare($consulta);
			//Ejecutar sentencia preparada
			$comando->execute(array($nro));
			//Capturar primera fila del resultado
			$row = $comando->fetch(PDO::FETCH_ASSOC);
			return $row;
		} catch (PDOException $e) {
			//Aqui puedes clasificar el error dependiendo 
			//de la excepcion para presentarlo en la respuesta Json
			return -1;
		}
	}/**
	*Obtiene los campos de un perfil de la linea 
	*el vehiculo y caractericas
	*con el nro de usuario
	*
	*@param $nro_linea
	*@return mixed (varias cosas en json)
	*/
	public static function perfil_vehiculo($nro_linea)
	{
		// consulta de la tabla de linea y registro
		$consulta = "call caracteristica_de_linea(?)";

		try {
			//preparar sentencia
			$comando = Database::getInstance()->getDb()->prepare($consulta);
			// Ejecutar la sentencia preparada
			$comando->execute(array($nro_linea));
			// Capturar la primera fila del resultado
			$row = $comando->fetch(PDO::FETCH_ASSOC);
			return $row;
			

		} catch (Exception $e) {
			// Aquí puedes clasificar el error dependiendo de la excepción
            // para presentarlo en la respuesta Json
            return -1;
        }
		
	}

	/**
	*Obtiene una lista de de pagos realizados y 
	*no realizados
	*
	*@param $nro_linea
	*@return una lista mixed
	*/
	public static function lista_de_pagos($nro_linea)
	{
		//llamando al procedimiento almacenado
		$consulta = "call Recibos(?)";

		try {
			//preparar sentencia 
			$comando = Database::getInstance()->getDb()->prepare($consulta);
			//Ejecutar sentencia preparada
			$comando->execute(array($nro_linea));
			//Capturar primera fila del resultado
			return $comando->fetchall(PDO::FETCH_ASSOC);


		} catch (Exception $e) {
			// Aquí puedes clasificar el error dependiendo de la excepción
            // para presentarlo en la respuesta Json
            return -1;
		}
	}
	/**
	*Obtiene los campos de ruta y turno de la linea
	*con el nro de linea o usuario
	*
	*@param $Linea nro de usuario
	*@return mixed
	*/
	public static function ruta_turno($Linea)
	{
		//llama al procedimiento almacenado
		$consulta = "call rutas_turno (?)";

		try {
			//preparar sentencia
			$comando = Database::getInstance()->getDb()->prepare($consulta);
			//Ejecutar Sentencia preparada
			$comando->execute(array($Linea));
			//capturar primera fila del resultado
			$row = $comando->fetch(PDO::FETCH_ASSOC);
			return $row;
			
		} catch (PDOException $e) {
			//Aqui puedes clasificar el error dependiendo 
			//de la excepcion para presentarlo en la respuesta Json
			return -1;
		}
	}

	/**
	* Obtiene el nro y contraseña para loguearse
	* a la base de datos
	* @param $nro    nro del usuario
	* @param $password    contraseña del usuario
	*/
	public static function login(
		$Nro,
		$password
		)
	{
		$_POST['password'] = $password;
		// Creando consulta para el usuario
		$consulta = "SELECT 
		              id_usuario,
		              Nro,
		              password
		              FROM usuario
		              WHERE
		                 Nro =?";

		try {
			// Preparar sentencia
			$comando = Database::getInstance()->getDb()->prepare($consulta);
			//Ejecutar sentencia preparada
			$comando->execute(array($Nro));
			//Capturar primera fila del resultado
			$row = $comando->fetch(PDO::FETCH_ASSOC);

			// la variable acotinuacion nos permitira determinar
		// si es o no la informacion correcta 
		// la inicializamos en false
		$validar_info = false;
		$login_ok = false;

		// vamos a buscar a toda la fila
		if ($row) {
			//si el password viene encryptado debemos desencryptarlo acá
            // ++ DESCRYPTAR ++//

        	//encaso que no lo este, solo comparamos como acontinuación
        	if ($_POST['password'] === $row['password']){
        		$login_ok = true;        		
        	} 
		}
		return $login_ok;
			
		} catch (PDOException $e) {
			//para testear pueden utilizar lo de abajo
            //die("la consulta murio " . $ex->getMessage());

            $respuesta["suceso"] = 0;
        	$respuesta["mensaje"] = "Problema con la base de datos, vuelve a intetarlo";
        	//print json_encode($response);
			
		}

		
	}



	
}

 ?>