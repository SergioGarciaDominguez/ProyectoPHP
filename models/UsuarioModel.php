<?php
require_once 'lib/Model.php';

class UsuarioModel extends Model
  {
  public $table;
  /**
   * @brief  Constructor de la clase.
   */
  public function __construct()
  {
    $this->table="usuario";
    parent::__construct();
  }
  /**
   * @brief  Devuelve todos los registros de la tabla.
   * @return  <Mixed> Array con los registros. False si no se ha encontrado registros.
   */
  public function getData($request)
  {
    return $this->getDataToGrid($request);
  }
  /**
   * @brief  Devuelve la tupla para el id especificado.
   * @param  <int>   $id    Id de la tupla a buscar.
   * @return  <Mixed> Array   con los valores de la tupla demandada. false si no se encuentra ningún registro.
   */
  public function getOne($id)
  {
    $query = "SELECT *
          FROM {$this->table}
          WHERE id=".Utils::escape($id);
    $row = $this->database->rawQuery($query,true);
    // Si se obtiene resultado.
    if ($row) {
      //Desencriptando los datos de conexiones
        if ($row[0]->conexiones != '') {
            $row[0]->conexiones =
                Mcrypt::decrypt($row[0]->conexiones,Session::getVariable('password2'));
        }
            if ($row[0]->admin != '')
                $row[0]->admin = Mcrypt::decrypt($row[0]->admin,Session::getVariable('password2'));

      return $row[0];
    } else {
      return false;
    }
  }
  /**
   * @brief  Devuelve todos los datos de la tabla.
   * @return  <Mixed> Array   con los valores de la tabla. false si no se encuentra ningún registro.
   */
  public function getAll()
  {
      $query = "SELECT * FROM {$this->table};";
      $row = $this->database->rawQuery($query,true);
      // Si se obtiene resultado.
      if ($row) {
        return $row;
      } else {
        return false;
      }
  }
  /**
   * @brief  Actualiza un registro
   * @param  <array> $post array post con los datos del formulario
   */
  public function update($post)
  {
      if ($post['nick'] == '') {
          throw NickIsRequired::run();
      }

      if ($post['email'] == '') {
          throw EmailIsRequired::run();
      }

      $values =
          [
              'nick' => $post['nick'],
              'email' => $post['email'],
              'first_name' => $post['first_name'],
              'last_name' => $post['last_name'],
              'fecha_modificacion' => $post['fecha_modificacion'],
              'id_usuario'=>$post['id_usuario']
          ];

      if ($_SESSION['is_admin']) {
          $values['is_admin'] = $post['is_admin'] ? 1 : 0;
      }

      if ($post['password'] != '') {
          if ($post['password'] != $post['retype_password']) {
              throw PasswordsNotEquals::run();
          }

          $values['password'] = sha1($post['password']);
          $this->passwordChanged = true;
      }

      $conditions =
          [
              'id' => $post['id']
          ];
      $this->database->update($this->table,$values,$conditions);
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
