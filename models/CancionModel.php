<?php

require_once 'lib/Model.php';

class CancionModel extends Model
  {
  public $table;
  const INFO_KEY = '5f84054f5397bb1d6bebe204ea4e2c6031006b88';
  /**
   * @brief  Constructor de la clase.
   */
  public function __construct()
  {
      $this->table="song";
      parent::__construct();
  }
  /**
   * @brief  Devuelve todos los registros de la tabla.
   * @return  <Mixed> Array con los registros. False si no se ha encontrado registros.
   */
  public function getData($request)
  {
      if (!isset($request['tabla'])) $request['tabla'] = $this->table;
      return $this->getDataToGrid($request);
  }
  /**
   * @brief  Devuelve la tupla para el id especificado.
   * @param  <int>   $id    Id de la tupla a buscar.
   * @return  <Mixed> Array   con los valores de la tupla demandada. false si no se encuentra ningún registro.
   */
  public function getOne($id)
  {
    if(is_numeric($id)) {
			$s_id = $id;
		} else {
			$s_id= mysql_real_escape_string($id);
    }
    $query = "SELECT *
          FROM {$this->table}
          WHERE id=".$s_id;
    $row = $this->database->rawQuery($query,true);
    // Si se obtiene resultado.
    if ($row)
    {
      //Desencriptando los datos de conexiones
            if ($row[0]->conexiones != '')
          $row[0]->conexiones = Mcrypt::decrypt($row[0]->conexiones,self::INFO_KEY);
            if ($row[0]->admin != '')
                $row[0]->admin = Mcrypt::decrypt($row[0]->admin,self::INFO_KEY);

      return $row[0];
    }
    else
      return false;
  }
  /**
   * @brief  Devuelve todos los datos de la tabla.
   * @return  <Mixed> Array   con los valores de la tabla. false si no se encuentra ningún registro.
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
   * @brief  Actualiza un registro
   * @param  <array> $post array post con los datos del formulario
   */
  public function update($post)
  {
    if(self::INFO_KEY)
    {
      $mcrypt = new Mcrypt();
      $visible=isset($_POST['visible']) ? 1 : 0;
      if ($_POST['caso'] == 'nuevo') { // Guarda datos al registro padre (nuevo)
        $values = array(
                  'nombre'=>$_POST['nombre'],
                  'contactos'=>$_POST['contactos'],
                  'conexiones'=>Mcrypt::encrypt(addslashes($_POST['conexiones']),self::INFO_KEY),
                  'notas'=>$_POST['notas'],
                  'logotipo'=>$_POST['logotipo'],
                  'visible'=>$visible,
                  'orden'=>$_POST['orden'],
                  'id_usuario'=>$_POST['id_usuario'],
                  'id_padre'=>$_POST['id_padre']
                );
      } elseif ($_POST['caso'] == 'recuperado' || $_POST['caso'] == 'anterior')  {  // Guarda datos para registro hijo (antiguo)
          $values = array(
                          'id_padre'=>$_POST['id_padre'],
                          'fecha_modificacion'=>$_POST['fecha_modificacion'],
                          'id_usuario'=>$_POST['id_usuario']
                        );
      } else { // Guarda datos para los registros hijos que cambian de padre
          $values = array('id_padre'=>$_POST['id_padre']);
      }
      if ($_SESSION['is_admin']) {
                $values['admin'] = Mcrypt::encrypt(addslashes($_POST['admin']),self::INFO_KEY);
            }

      $conditions = array('id'=>$_POST['id']);
      $this->database->update($this->table,$values,$conditions);

      return true;
    }
    else
    {
      return false;
    }
  }
  /**
   * @brief  borra un registro
   * @param  <Int> $id id del registro a borrar
   * @return  <Bool> true si se ha borrado correctamente, false en caso contrario
   */
  public function delete($id)
  {
    $conditions = array('id'=>$id);
    return $this->database->delete($this->table,$conditions);
  }

  /**
   * @brief  Cambia la encriptación
   * @param  <String> $oldKey antigua clave de cifrado
   * @param  <String> $newKey nueva clave de cifrado
   * @return  <Bool> true si se ha completado correctamente
   */
  public function changeEncryptation($oldKey,$newKey)
  {
      //
  }
}
