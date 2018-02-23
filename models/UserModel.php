<?php

/**
 * @brief	Clase User
 */
class UserModel extends Model
{
	/**
	 * @brief	Constructor de la clase.
	 */
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * @brief	Devuelve la tupla para el id especificado.
	 * @param	<int> 	$id		Id de la tupla a buscar.
	 * @return	<Mixed> Array 	con los valores de la tupla demandada. false si no se encuentra ningún registro.
	 */
	public function userLogin ($username, $password)
	{

		$username = mysqli_real_escape_string($this->database->getConn(),$username);

		$query = "SELECT id, nick FROM usuarios WHERE nick='".$username."' AND password='".sha1($password)."'";
		//die($query);

		$row = $this->database->rawQuery($query, true);
		// Si se obtiene resultado.
		if ($row)
			return $row[0];
		else
			return false;

    }

	/**
	 * @brief	Cambia la password del administrador
	 * @param	<String> $password	password a cambiar
	 */
	public function changePassword ($password)
	{
		$query = "UPDATE usuarios SET password='".sha1($password.KEY)."'";
		$this->database->rawQuery($query);
    }



}
