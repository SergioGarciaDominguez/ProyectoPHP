<?php

/**
 * @brief	Clase encargada de todo lo referente al manejo de sesiones de la 
 * 			web.
 */
class Session
{
	// ID de la sesión
	static $sessionId;

    /**
     * @brief   Se privatiza el constructor para que no se puedan realizar 
     *          isntancias de ésta clase.
     */
    private function __construct() { }

	/**
	 * @brief	Crea la sesión con el identificador indicado siempre y cuando 
	 * 			no exista una con el mismo identificador.
	 *
	 * @param	<String> $id	Nombre de la sesión.
	 */
	static function create($id = null)
	{
		self::$sessionId = isset(self::$sessionId) ? self::$sessionId : $id;

		if (self::$sessionId)
		{
			session_name(self::$sessionId);
			session_start();
		}
		else
		{
			echo 'Debes pasar un nombre de sesión para instanciarla';
		}
	}

	/**
	 * @brief	Alias de create(). Solo por cuestión semántica. Usar este método 
	 *			si la ya está iniciada en otra página.
	 */
	static function resume()
	{
		self::create(self::getIdentifier());
	}

	/**
	 * @brief	Obtiene el identificador de la sesión.
	 *
	 * @return	<String> ID de la sesión actual.
	 */
	static function getIdentifier()
	{
		return ADMIN_SESSION_ID;
	}

	/**
	 * @brief	Establece las variables pasadas en la sesión actual.
	 *
	 * @param	<array> Lista de pares clave-valor con las variables 
	 * 					a establecer en la sesión actual.
	 *
	 * @see		Session::setVariable()
	 */
	static function setVariables(array $values)
	{
		foreach ($values as $key => $value)
		{
			self::setVariable($key, $value);
		}
	}

	/**
	 * @brief	Establece una variable en la sesión actual.
	 *
	 * @param	<String>	$key	Identificador de la variable.
	 * @param	<Mixed>		$value	Valor de la variable.
	 */
	static function setVariable($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	/**
	 * @brief	Obtiene una variable de sesión.
	 *
	 * @param	<String> $variable	Identificador de la variable que se desea 
	 * 								obtener.
	 *
	 * @return	<Mixed> La variable de sesión solicitada.
	 */
	static function getVariable($variable)
	{
		if (isset($_SESSION[$variable]))
		{
			return $_SESSION[$variable];
		}
		else
		{
			//echo 'La variable de sesión ' . $variable . ' no existe...';
			return false;
		}
	}

	/**
	 * @brief	Elimina una variable de sesión.
	 *
	 * @param	<String> $variable	Identificador de la variable que se desea 
	 * 								eliminar de la sesión actual.
	 */
	static function unsetVariable($variable)
	{
		unset($_SESSION[$variable]);
	}

	/**
	 * @brief	Destruye la sesión actual, junto con todas sus cookies.
	 */
	static function destroy()
	{
		session_name(self::getIdentifier());
		session_unset();
		session_destroy();
	}
}
