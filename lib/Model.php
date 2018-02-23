<?php

require_once 'lib/MySQL.php';

/**
 * @brief   Clase padre de los modelos de la aplicación. Se encarga de realizar la conexión a la base de datos y del tratamiento de los resultados.
 */
class Model
{
  // Base de datos a la que ataca el modelo
  protected $database;

  // Campos del modelo
  protected $fields = array();

  // Tabla asociada al modelo
  protected $table = '';


  public function table() { return $this->table; }
  public function fields() { return $this->fields; }

  /**
   * @brief  Constructor de la clase. Simplemente instancia un nuevo objeto MySQL.
   * @see    MySQL
   */
  function __construct()
  {
    $this->database = new MySQL();
  }

  /**
   * @brief  modelo a usar en la lógica de negocio.
   * @param  <Array>    $request  Array con las variables enviadas por la url
   * @return  <String>  $output    Datos codificados en json
   */
  protected function getDataToGrid($request)

  {

    /*
     * MySQL connection
     */
    $mysqli = new mysqli('p:' . MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD) or
      die( 'Could not open connection to server' );

    $mysqli->select_db(MYSQL_DATABASE) or
      die( 'Could not select database '. $gaSql['db'] );

    $mysqli->set_charset('utf8');



    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";

    /* DB table to use */
    $sTable = $request['tabla'];

    if ($request['columnas'] = '*') {
        $sql ="DESCRIBE {$sTable}";
        $res = mysqli_query($mysqli, $sql);
        $aColumns = array();
        $count = 0;
        while ($records = mysqli_fetch_assoc($res)) {
            $aColumns[$count] = $records['Field'];
            $count++;
        }
    } else {
      $aColumns = explode(',',$request['columnas']);
    }

    /*
     * Paging
     */
    $sLimit = "";
    if (isset($request['iDisplayStart']) && $request['iDisplayLength'] != '-1') {
        $sLimit = "LIMIT " . $mysqli->real_escape_string($request['iDisplayStart'])  .", " .
        $mysqli->real_escape_string( $request['iDisplayLength'] );
    }

    /*
     * Ordering
     */
    if ( isset( $request['iSortCol_0'] ) ) {
        $sOrder = "ORDER BY  ";
        for ( $i=0 ; $i<intval( $request['iSortingCols'] ) ; $i++ ) {
            if ($request['bSortable_'.intval($request['iSortCol_'.$i])] == "true") {
                $sOrder .= $aColumns[ intval( $request['iSortCol_'.$i] ) ]."
                ".$mysqli->real_escape_string( $request['sSortDir_'.$i] ) .", ";
            }
        }

        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( $sOrder == "ORDER BY" ) {
            $sOrder = "";
        }
    }

    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    if ( $request['sSearch'] != "" )
    {
      $sWhere = "WHERE (";
      for ( $i=0 ; $i<count($aColumns) ; $i++ )
      {
        $sWhere .= $aColumns[$i]." LIKE '%".$mysqli->real_escape_string( $request['sSearch'] )."%' OR ";
      }
      $sWhere = substr_replace( $sWhere, "", -3 );
      $sWhere .= ')';
    }

    /* Individual column filtering */
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($request['bSearchable_' . $i] == "true" && $request['sSearch_'.$i] != '') {
            if ( $sWhere == "" ) {
              $sWhere = "WHERE ";
            } else {
              $sWhere .= " AND ";
            }
            $sWhere .= $aColumns[$i]." LIKE '%".$mysqli->real_escape_string($request['sSearch_'.$i])."%' ";
        }
    }

    //  Conditions
    if (isset($request['conditions'])&&$request['conditions'] != "") {
        if ( $sWhere == "" ) {
            $sWhere = "WHERE ";
        } else {
            $sWhere .= " AND ";
        }

        $sWhere .= $request['conditions'];
    }

    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "
      SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
      FROM   $sTable
      $sWhere
      $sOrder
      $sLimit
    ";

    $rResult = $mysqli->query($sQuery) or die($mysqli->error);
    /* Data set length after filtering */
    $sQuery = "
      SELECT FOUND_ROWS()
    ";
    $rResultFilterTotal = $mysqli->query($sQuery) or die($mysqli->error);
    $aResultFilterTotal = $rResultFilterTotal->fetch_assoc();
    $iFilteredTotal = $aResultFilterTotal[0];

    /* Total data set length */
    $sQuery = "
      SELECT COUNT(".$sIndexColumn.")
      FROM   $sTable
    ";
    $rResultTotal = $mysqli->query($sQuery) or die($mysqli->error);
    $aResultTotal = $rResultTotal->fetch_assoc();
    $iTotal = $aResultTotal[0];

    /*
     * Output
     */
    $output = array(
      "sEcho" => intval($request['sEcho']),
      "iTotalRecords" => $iTotal,
      "iTotalDisplayRecords" => $iFilteredTotal,
      "aaData" => array(),
      "types" => array()
    );

    while ( $aRow = $rResult->fetch_assoc() )
    {
      $row = array();
      for ( $i=0 ; $i<count($aColumns) ; $i++ )
      {
        //TYPE
        $sql ="DESCRIBE {$sTable}";
        $res = mysqli_query($mysqli, $sql);
        while ($records = mysqli_fetch_assoc($res)) {
          if($records['Field']==$aColumns[$i]){
            $output['types '][] = $records['Type'];
          }
        }
        mysqli_free_result($res);
        //VERSION
        if ( $aColumns[$i] == "version" )
        {
          /* Special output formatting for 'version' column */
          $row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
        }
        else if ( (strpos($aColumns[$i], "imagen")!== FALSE) || ($aColumns[$i] == "logo") )
        {
          /* show image */
          if ($aRow[ $aColumns[$i] ]!=""&&file_exists(IMAGE_PATH.$aRow[ $aColumns[$i] ]))
            $row[] = "<img src=\"".BASE_URL.IMAGE_PATH.$aRow[ $aColumns[$i] ]."\" height=\"100\"/>";
          else $row[]="";
        }
        //OTHERS
        else if ( $aColumns[$i] != ' ' )
        {
          /* General output */
          $row[] = $aRow[ $aColumns[$i] ];
        }
      }
      $output['aaData'][] = $row;
    }
    //print_r($output);


    return json_encode( $output );
  }


  /**
   * @brief  Obtiene el nombre de una imagen
   * @param   <String>   Table   Nombre de la tabla
   *      <Int>     Id   del registro
   *      <Int>     Index  Indice de la imagen
   *      <Boolean>   Flag  Indica si es el logo
   * @return  Nombre de la imagen
   */
  public function getImageName($table, $id, $numImages, $logo=false)
  {
    if ( $logo === false ) {
      $arrFields = array();
      for($i=1; $i<=$numImages; $i++)
      {
        $arrFields[] = 'imagen'.$i;
      }
      $fields = implode(',',$arrFields);
    }
    else {
      $fields = 'logo';
    }
    $query = "SELECT $fields FROM $table WHERE id='$id'";
    $result = $this->database->rawQuery($query);
    return $result[0];
  }
  /**
   * @brief  Obtiene el nombre de un fichero
   * @param   <String>   Table   Nombre de la tabla
   *      <Int>     Id   del registro
   *      <Int>     Index  Indice del fichero
   * @return  Nombre del fichero
   */
  public function getFileName($table, $id, $numFiles)
  {
    $arrFields = array();
    for($i=1; $i<=$numFiles; $i++)
    {
      $arrFields[] = 'fichero'.$i;
    }
    $fields = implode(',',$arrFields);
    $query = "SELECT $fields FROM $table WHERE id='$id'";
    $result = $this->database->rawQuery($query);
    return $result[0];
  }
  /**
   * @brief  Obtiene el nombre de un fichero con idioma
   * @param   <String>   Table   Nombre de la tabla
   *      <Int>     Id   del registro
   *      <String>   Nombre del campo
   * @return  Nombre del fichero
   */
  public function getFileNameIdioma($table, $id, $field)
  {
    $query = "SELECT $field FROM $table WHERE id='$id'";
    $result = $this->database->rawQuery($query);
    return $result[0];
  }



  /**
   * @brief  Actualiza el nombre de una imagen en la base de datos
   * @param  <String> $table Nombre de la tabla a la que afectar
   * @param   <String> $field Nombre del campo al que afectar
   * @param  <String> $name  Nombre del fichero a almacenar
   */
  public function updateImageFile($table, $field, $id, $name)
  {
    $values = array($field=>$name);
    $conditions = array('id'=>$id);
    $this->database->update($table,$values,$conditions);
  }



  /**
   * @brief  Inserta una fila vacia en una tabla
   * @param  <String> $table Nombre de la tabla a la que afectar
   * @return  <Int>   $id  nuevo id en la tabla
   */
  public function addEmptyRow($table, $values=[])
  {
    date_default_timezone_set('Europe/Madrid');

    if (count($values) == 0)
        {
            $values = array('fecha_insercion'=>date('Y-m-d H:i:s'));
        }

    $row = $this->database->insert($table,$values);
    return $row['id'];
  }



  /**
   * @brief  Obtiene datos para rellenar un combo
   * @param   <String> table       Nombre de la tabla
   *      <Array>   fields     Array con los campos a seleccionar
   *      <Int>   selected    id del registro a establecer como seleccionado
   *      <Array>   whereFields  Array con los nombres de los campos a usar en la consulta where
   * @return  <Array>  Array con los campos seleccionados para el combo
   */
  public function getRowsToHtmlSelect($table, $field, $selected, $whereFields=Null)
  {
    $query = "SELECT id, $field AS titulo FROM $table";
    if(isset($whereFields))
    {
      $queryWhere = " WHERE ";
      foreach($whereFields as $key => $value)
      {
        $arrWhere[] = $key . "='" . $value . "'";
      }
      $queryWhere .= implode(' AND ' . $arrWhere);
    }
    $result = $this->database->rawQuery($query, true);
    return $result;
  }

  public function startTransaction()
    {
        $this->database->startTransaction();
    }

    public function commitTransaction()
    {
        $this->database->commitTransaction();
    }

    public function rollbackTransaction()
    {
        $this->database->rollbackTransaction();
    }


  /**
   * @brief  Destructor del objeto. Destruye el objeto MySQL, causando el
   *       cierre de la conexión a la base de datos.
   */
  public function __destruct()
  {
    unset($this->database);
  }

}
