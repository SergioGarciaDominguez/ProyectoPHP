<?php
require_once 'lib/Model.php';

class MiCancionModel extends Model
	{
	public $table;
	/**
	 * @brief	Constructor de la clase.
	 */
	public function __construct()
	{
		$this->table="mi_cancion";
		parent::__construct();
	}
	/**
	 * @brief	Devuelve todos los registros de la tabla.
	 * @return	<Mixed> Array con los registros. False si no se ha encontrado registros.
	 */
	public function getData($request)
	{
		return $this->getDataToGrid($request);
	}
	/**
	 * @brief	Devuelve la tupla para el id especificado.
	 * @param	<int> 	$id		Id de la tupla a buscar.
	 * @return	<Mixed> Array 	con los valores de la tupla demandada. false si no se encuentra ningún registro.
	 */
	public function getOne($id)
	{
		$query = "SELECT *
				  FROM {$this->table}
				  WHERE id=".Utils::escape($id);
		$row = $this->database->rawQuery($query,true);
		// Si se obtiene resultado.
		if ($row)
			return $row[0];
		else
			return false;
	}
	/**
	 * @brief	Devuelve todos los datos de la tabla.
	 * @return	<Mixed> Array 	con los valores de la tabla. false si no se encuentra ningún registro.
	 */
	public function getAll()
	{
		$query = "SELECT *
				  FROM {$this->table} ";
		$row = $this->database->rawQuery($query,true);
		// Si se obtiene resultado.
		if ($row)
			return $row;
		else
			return false;
	}
	/**
	 * @brief	Actualiza un registro
	 * @param	<array> $post array post con los datos del formulario
	 */
	public function update($post)
	{
		$visible=isset($_POST['visible']) ? 1 : 0;
		$values = array(
						  'tituloi'=>$_POST['nombre'],
						  'base_de_datos'=>$_POST['base_de_datos'],
						  'maquetado'=>$_POST['maquetado'],
						  'php'=>$_POST['php'],
						  'servidor'=>$_POST['servidor'],
						  'visible'=>$visible,
						  'orden'=>$_POST['orden'],
              'fecha_modificacion'=>$_POST['fecha_modificacion'],
              'id_usuario'=>$_POST['id_usuario']
		);
		$conditions = array('id'=>$_POST['id']);
		$this->database->update($this->table,$values,$conditions);
	}
	/**
	 * @brief	borra un registro
	 * @param	<Int> $id id del registro a borrar
	 * @return	<Bool> true si se ha borrado correctamente, false en caso contrario
	 */
	public function delete($id)
	{
		$conditions = array('id'=>$id);
		return $this->database->delete($this->table,$conditions);
	}
}
