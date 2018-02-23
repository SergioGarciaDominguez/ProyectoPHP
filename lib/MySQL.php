<?php

final class MySQLJoin
{
    private $type;
    private $table;
    private $on;

    public function on() { return $this->on; }
    public function table() { return $this->table; }
    public function type() { return $this->type; }

    public function __construct($type, $table, $on)
    {
        $this->type = $type;
        $this->table = $table;
        $this->on = $on;
    }
}

final class MySQLQuery
{
    private $type;
    private $fields;
    private $table;
    private $joins;
    private $conditions;
    private $values;

    public function setType($type) { $this->type = $type; }
    public function setFields(array $fields) { $this->fields = $fields; }
    public function setTable($table) { $this->table = $table; }
    public function setJoins(array $joins) { $this->joins = $joins; }
    public function setConditions(array $conditions) { $this->conditions = $conditions; }
    public function setValues(array $values) { $this->values = $values; }
    public function type() { return $this->type; }
    public function fields() { return $this->fields; }
    public function table() { return $this->table; }
    public function joins() { return $this->joins; }
    public function conditions() { return $this->conditions; }
    public function values() { return $this->values; }

    public function __construct($type = null, $table = null, $criteria = array())
    {
        $this->type = $type;
        $this->table = $table;

        $this->fields = isset($criteria['fields']) ? $criteria['fields'] : array();
        $this->joins = isset($criteria['joins']) ? $criteria['joins'] : array();
        $this->conditions = isset($criteria['conditions']) ? $criteria['conditions'] : array();
        $this->values = isset($criteria['values']) ? $criteria['values'] : array();
    }

    public function addField($field) { $this->fields[] = $field; }
    public function addCondition($condition) { $this->conditions[] = $condition; }
    public function addJoin(MySQLJoin $join) { $this->joins[] = $join; }
    public function addValue($value) { $this->values[] = $value; }
}

/**
 * @brief   Clase de operaciones con la base de datos. Se encontrará instanciada
 *       en cada uno de los modelos.
 */
final class MySQL
{
    // Constantes de tipo de consultas
    const QUERY_TYPE_SELECT = 'SELECT';
    const QUERY_TYPE_INSERT = 'INSERT';
    const QUERY_TYPE_UPDATE = 'UPDATE';
    const QUERY_TYPE_DELETE = 'DELETE';
    const LEFT_JOIN = 'LEFT';

    // Referencia a la conexión
    private $conn;

    /**
     * @brief   Constructor de la clase. Se encarga de conectar a la base de
     *       datos, amén de otras cosas, usando los parámetros presentes en
     *       config/db.php
     */
    public function __construct()
    {
        $this->conn = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);

        if (mysqli_connect_error())
        {
          //Logger::log('Error de conexión a la base de datos (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        }
        else
        {
          mysqli_set_charset($this->conn, MYSQL_ENCODING);
        }
    }

    /**
       * @brief    Devuelve el identificador de la conexión
       * @return  Objeto conexión
       */
      public function getConn() { return $this->conn; }

    /**
     * @brief  Método público con el que se realizan las consultas a la base
     *       de datos usando un objeto de clase MySQLQuery.
     *
     * @param  <MySQLQuery> $query Objecto que almacena la consulta
     *                 a realizar.
     *
     * @return  <Mixed> Resultado de la consulta.
     */
    public function performQuery(MySQLQuery $query)
    {
        $type = $query->type();
        switch ($type) {
            case self::QUERY_TYPE_SELECT:
                return $this->select($query->table(), $query->fields(), $query->joins(), $query->conditions());
                break;
            case self::QUERY_TYPE_INSERT:
                return $this->insert($query->table(), $query->values());
                break;
            case self::QUERY_TYPE_UPDATE:
                return $this->update($query->table(), $query->values(), $query->conditions());
                break;
                return $this->update($query->table(), $query->values(), $query->conditions());
                break;
            case self::QUERY_TYPE_DELETE:
                return $this->delete($query->table(), $query->conditions());
                break;
            default:
                break;
        }
    }

    /**
     * @brief Comienza una transacción
     */
      public function startTransaction()
      {
          mysqli_autocommit($this->conn, false);
      }

    /**
     * @brief Confirma la actual transacción
     *
     */
      public function commitTransaction()
      {
          try
          {
              mysqli_commit($this->conn);
          }
          catch (Exception $ex)
          {
              throw new MySQLException(mysqli_errno($this->conn), mysqli_error($this->conn));
          }
      }

    /**
     * @brief Revierte una transacción
     */
      public function rollbackTransaction()
      {
          mysqli_rollback($this->conn);
          mysqli_autocommit($this->conn, true);
      }

    /**
     * @brief  Realiza una consulta.
     *
     * @param  <String> $sql      Consulta SQL a realizar
     *
     * @return   <Mixed>   false si algo ha ido mal. En caso de que todo esté
     *            correcto, se devuelve un array asociativo con el resultado
     *            de la consulta.
     */
      public function rawQuery($sqlString, $object=false)
      {
          try
          {
              $res = mysqli_query($this->conn, $sqlString);

              if ($res)
              {
          $records = array();
          if(!$object)
          {
            while($record = mysqli_fetch_array($res))
            {
              $records[] = $record;
            }
          }
          else
          {
            while($record = mysqli_fetch_object($res))
            {
              $records[] = $record;
            }
          }
          mysqli_free_result($res);

                  return $records;
              }
              else
              {
                  throw new MySQLException(mysqli_errno($this->conn), mysqli_error($this->conn));
              }
          }
          catch (MySQLException $ex)
          {
              //Logger::log($ex->errorCode() . ' - ' . $ex->errorMessage());
              //Logger::log($sqlString);

              return false;
          }
      }

    /**
     * @brief  Realiza una consulta de tipo SELECT.
     *
     * @param  <String> $table      Tabla a la que realizar la consulta.
     * @param  <Array>  $fields    Array de cadenas que representan los campos
     *                  a obtener.
     * @param  <Array>   $joins      Array de objetos MySQLJoin con los joins de
     *                   la consulta.
     * @param  <Array>   $conditions  Array de cadenas con cada una de las
     *                   condiciones WHERE a aplicar en la
     *                   consulta.
     *
     * @return   <Mixed>   false si algo ha ido mal. En caso de que todo esté
     *            correcto, se devuelve un array asociativo con el resultado
     *            de la consulta.
     */
    public function select($table, array $fields, array $joins = array(), array $conditions = array())
    {
      $f = '';
      if ($fields[0] == '*')
      {
        $f = '*';
      }
      else
      {
        foreach ($fields as $index => $field)
        {
          $f .= $field;
          $f .= $index < count($fields) - 1 ? ', ' : '';
        }
      }

      $j = '';
      foreach ($joins as $join)
      {
        $j .= $join->type() . ' JOIN ' . $join->table() . ' ON ' . $join->on() . ' ';
      }

          $x = 0;
      $c = !empty($conditions) ? 'WHERE ' : '';
      foreach ($conditions as $key => $value)
      {
              if (is_int($value) || is_float($value))
              {
                  $value = mysqli_real_escape_string($this->conn, $value);
              }
              else
              {
                  $value = '"' . mysqli_real_escape_string($this->conn, $value) . '"';
              }

        $c .= ' ' . $key . ' = ' . $value;
        $c .= $x < count($conditions) - 1 ? ' AND ' : '';

              $x++;
      }

      $sql = <<<SQL
			SELECT {$f} FROM {$table} {$j} {$c}
SQL;

      try
      {
        $res = mysqli_query($this->conn, $sql);

        if ($res)
        {
          $records = mysqli_fetch_assoc($res);
          mysqli_free_result($res);

          return $records;
        }
        else
        {
          throw new MySQLException(mysqli_errno($this->conn), mysqli_error($this->conn));
        }
      }
      catch (MySQLException $ex)
      {
        //Logger::log($ex->errorCode() . ' - ' . $ex->errorMessage());
        //Logger::log($sql);

        return false;
      }
    }

    /**
     * @brief  Realiza una consulta de tipo INSERT.
     *
     * @param  <String> $table    Tabla a la que realizar la consulta.
     * @param  <Array>  $values  Array asociativo con pares clave-valor
     *                 donde la clave es el campo de la tabla a
     *                 insertar el valor.
       *
       * @throws  <MySQLException>    Si hay algún error a la hora de realizar la
       *                              insercción.
     *
     * @return   <Mixed>   false si algo ha ido mal. En caso de que todo esté
     *            correcto, se devuelve un array asociativo con la tupla
     *            insertada.
     */
    public function insert($table, array $values = array())
    {
      $x = 0;
      $s = '';
      foreach ($values as $key => $value)
          {
              if (is_int($value) || is_float($value))
              {
                  $value = mysqli_real_escape_string($this->conn, $value);
              }
              else
              {
                  $value = '"' . mysqli_real_escape_string($this->conn, $value) . '"';
              }

        $s .= ' ' . $key . ' = ' . $value;
        $s .= $x < count($values) - 1 ? ', ' : '';

        $x++;
      }

      $sql = <<<SQL
        INSERT INTO {$table} SET {$s}
SQL;
      try
      {
        $res = mysqli_query($this->conn, $sql);

        if ($res)
        {
          $fields = array('*');

          $conditions = array(
             'id' => mysqli_insert_id($this->conn)
          );

          return $this->performQuery(new MySQLQuery('SELECT', $table, array('fields' => $fields, 'conditions' => $conditions)));
        }
        else
        {
          throw new MySQLException(mysqli_errno($this->conn), mysqli_error($this->conn));
        }
      }
      catch (MySQLException $ex)
      {
        //Logger::log($ex->errorCode() . ' - ' . $ex->errorMessage());
        //Logger::log($sql);

              throw $ex;
      }
    }

    /**
     * @brief  Realiza una consulta de tipo UPDATE.
     *
     * @param  <String> $table      Tabla a la que realizar la consulta.
     * @param  <Array>  $values    Array asociativo con pares clave-valor
     *                   donde la clave es el campo de la tabla a
     *                   actualizar su valor.
     * @param  <Array>   $conditions  Array de cadenas con las condiciones
     *                   a aplicar a la consulta.
     *
       * @throws  <MySQLException>    Si hay algún error a la hora de realizar la
       *                              actualización.
       *
     * @return   <Mixed>   false si algo ha ido mal. En caso de que todo esté
     *            correcto, se devuelve un array asociativo con las tuplas
     *            actualizadas.
     */
    public function update($table, array $values = array(), array $conditions = array())
    {
      $x = 0;
      $v = '';
      foreach ($values as $key => $value)
      {
              if (is_int($value) || is_float($value))
              {
                  $value = mysqli_real_escape_string($this->conn, $value);
              }
              else
              {
                  $value = '"' . mysqli_real_escape_string($this->conn, $value) . '"';
              }

        $v .= ' ' . $key . ' = ' . $value;
        $v .= $x < count($values) - 1 ? ', ' : '';

        $x++;
      }

      $x = 0;
      $c = !empty($conditions) ? 'WHERE ' : '';
      foreach ($conditions as $key => $value)
      {
              if (is_int($value) || is_float($value))
              {
                  $value = mysqli_real_escape_string($this->conn, $value);
              }
              else
              {
                  $value = '"' . mysqli_real_escape_string($this->conn, $value) . '"';
              }

        $c .= ' ' . $key . ' = ' . $value;
        $c .= $x < count($conditions) - 1 ? ' AND ' : '';

        $x++;
      }

      $sql =<<<SQL
        UPDATE {$table} SET {$v} {$c}
SQL;
      try
      {
        $res = mysqli_query($this->conn, $sql);
        if ($res)
        {
          $fields = array('*');

          return $this->performQuery(new MySQLQuery('SELECT', $table, array('fields' => $fields, 'conditions' => $conditions)));
        }
        else
        {
          throw new MySQLException(mysqli_errno($this->conn), mysqli_error($this->conn));
        }
      }
      catch (MySQLException $ex)
      {
        //Logger::log($ex->errorCode() . ' - ' . $ex->errorMessage());
        //Logger::log($sql);

              throw $ex;
      }
    }

    /**
     * @brief  Realiza una consulta de tipo DELETE.
     *
     * @param  <String> $table      Tabla a la que realizar la consulta.
     * @param  <Array>   $conditions  Array de cadenas con las condiciones
     *                   a aplicar a la consulta.
       *
       * @throws  <MySQLException>    Si hay algún error a la hora de realizar el
       *                              borrado.
       *
     * @return   <Boolean> false si algo ha ido mal. true en caso de que todo esté
     *             correcto.
     */
    public function delete($table, array $conditions = array())
    {
      $x = 0;
      $c = !empty($conditions) ? 'WHERE ' : '';
      foreach ($conditions as $key => $value)
      {
              if (is_int($value) || is_float($value))
              {
                  $value = mysqli_real_escape_string($this->conn, $value);
              }
              else
              {
                  $value = '"' . mysqli_real_escape_string($this->conn, $value) . '"';
              }

        $c .= ' ' . $key . ' = ' . $value;
        $c .= $x < count($conditions) - 1 ? ' AND ' : '';

        $x++;
      }

      $sql =<<<SQL
        DELETE FROM {$table} {$c}
SQL;

      try
      {
        $res = mysqli_query($this->conn, $sql);


        if ($res)
        {
          return true;
        }
        else
        {
          throw new MySQLException(mysqli_errno($this->conn), mysqli_error($this->conn));
        }
      }
      catch (MySQLException $ex)
      {
        //Logger::log($ex->errorCode() . ' - ' . $ex->errorMessage());
        //Logger::log($sql);

        throw $ex;
      }
    }

    /**
     * @brief  Destructor de la clase. Cierra la conexión abierta. Nótese que
     *       debe realizar un unset() a la variable $_DB al final de la
     *       ejecución de la aplicación.
     */
    public function __destruct()
    {
      mysqli_close($this->conn);
    }

}

final class MySQLException extends Exception
{
  private $errorCode;
  private $erroMessage;

  public function errorCode() { return $this->errorCode; }
  public function errorMessage() { return $this->errorMessage; }

  public function __construct($errorCode, $errorMessage)
  {
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }
}
