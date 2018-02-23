<?php
/**
 *  @brief Clase abstracta de la que heredan todos y cada uno de los controladores de la aplicación
 */
class Controller
{

    /**
    * @brief <String> Variables para cargar la vista
    */
    protected $view = '';


  /**
    * @brief <String> Variables para cargar el fichero js correspondiente
    */
    protected $js = '';


  /**
    * @brief <String> Variables para ficheros e imagenes
    */
    protected $numImages = 0;
    protected $numFiles = 0;

    protected $user;

  /**
   * @brief Constructor de la clase.
   */
  public function __construct()
  {

    }

  /**
  * @brief   Método getter para la propiedad view
  * @return   Nombre del template a cargar
    **/
  public function getView() {  return $this->view; }

  /**
  * @brief   Método setter para la propiedad view
    **/
  public function setView($view)  { $this->view = $view; }


  /**
  * @brief   Método getter para la propiedad js
  * @return   Nombre del fichero a cargar
    **/
  public function getJs() {  return $this->js; }

  /**
  * @brief   Método setter para la propiedad js
    **/
  public function setJs($js)  { $this->js = $js; }


  /**
  * @brief   Método getter para la propiedad numImages
  * @return   Número de imágenes
    **/
  public function getNumImages() {  return $this->numImages; }

  /**
  * @brief   Método setter para la propiedad numImages
    **/
  public function setNumImages($images)  { $this->numImages = $images; }

  /**
  * @brief   Método getter para la propiedad numFiles
  * @return   Número de imágenes
    **/
  public function getNumFiles() {  return $this->numFiles; }

  /**
  * @brief   Método setter para la propiedad numFiles
    **/
  public function setNumFiles($Files)  { $this->numFiles = $Files; }

    /**
     * @brief Redirige la petición a la url especificada.
     * @param <String> $location URL a la que se desea realizar la redirección.
     */
    public function redirect($location)
    {
        header('Location: ' . BASE_URL . $location);
    }


  /**
     * @brief Compone el nombre amigable para un fichero
     * @param <String> Nombre generado para el fichero
     */
    public function makeName($name)
    {
        $prefix = date('YmdHis');
    $friendly = Utils::friendlyFileName($name);
    return $prefix.'-'.$friendly;
    }


  /**
     * @brief Graba fisicamente el fichero en la carpeta uploads del proyecto
     * @param <String> $origin nombre del fichero origen
   * @param <String> $destination nombre del fichero de destino
     */
    public function saveFileToDisk($origin, $destination)
    {
        $thumb = new Easyphpthumbnail;
        $thumb -> Thumbsize = IMAGE_SIZE;
        $thumb -> Thumblocation = IMAGE_PATH;
        //$thumb -> Thumbsaveas = 'png';
        $thumb -> Thumbfilename = $destination;
        $thumb -> Createthumb($origin,'file');
        return true;
    }


  /**
     * @brief Selecciona datos de una tabla para llenar las etiquetas option de un select
     * @param <String> html options para un select
     */
    public function printSelect($table, $field, $selected, $whereFields=Null)
    {
        $obj = new Model;
        $rows = $obj->getRowsToHtmlSelect($table, $field, $selected, $whereFields);
        if (isset($rows)) {
            foreach($rows as $row) {
                if($row->id == $selected) $htmlSelected = ' selected="selected"'; else $htmlSelected = '';
                $html .= '<option value="'.$row->id.'"'.$htmlSelected.'>'.$row->titulo.'</option>';
            }
        }
        return $html;
    }

    public function user()
    {
        return $this->user;
    }

    public function setUser($id)
    {
        $obj = new UsuariosModel();
        $user = $obj->getOne($id);
        $this->user = $user;
        if ($this->user)
        {
            $_SESSION['is_admin'] = $this->user->is_admin;
        }


    }

    public function callWebService($param)
    {
        $url_param = '';

        foreach ($param as $key => $value) {
          $url_param .= '&' . $key . '=' . $value;
        }

        $url ="http://ws.audioscrobbler.com/2.0/?$url_param";
        $json = file_get_contents($url);
        $json = json_decode(str_replace('#text', 'text', json_encode($json)));
        return $json;
    }


}
