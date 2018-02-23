<?php

require_once 'lib/Controller.php';

class ArtistaController extends Controller
{
    public function __construct()
    {
        $this->setNumImages(0);
        $this->setNumFiles(0);
    }
    public function grid()
    {
        $this->setView('views/artistaList.tpl.php');
        $this->setJs('js/artistaList.js');
    }
    public function getData()
    {
        $objArtista = new ArtistaModel();
        $data = $objArtista->getData($_REQUEST);
        $this->setJs('artista.js');
        echo $data;
    }
    public function edit()
    {
        $id = $_REQUEST['id'];
        $objArtista = new ArtistaModel();
        $row = $objArtista->getOne($id);
        if ($row) {
            $this->setView('artistaView.tpl.php');
            $this->setJs('artistaView.js');
            return $row;
        } else {
            $this->redirect('index.php?c=Artista&m=grid');
            die();
        }
    }
    public function update()
    {
        if(!isset($_POST['id'])) {
            $this->redirect('index.php?c=Artista&m=grid');
            die();
        }

        $objArtista = new ArtistaModel();
        $id = $_POST['id'];
        $padre = $_POST['id_padre'];
        $_POST['id_usuario'] = Session::getVariable('user_id');
        $_POST['fecha_modificacion'] = date('Y-m-d H:i:s');

        //es histórico

        if ($_POST['es_historico']) {
            $_POST['id_padre'] = 0;
            $_POST['caso'] = 'recuperado';
            $retorno = $objArtista->update($_POST); // Colocamos al actual el padre = 0
            $peticion_hijos = [
              'tabla' => 'artista',
              'columnas' => 'id',
              'conditions' => "id_padre=$padre"
            ];
            $hijos = json_decode($objArtista->getData($peticion_hijos));  // Consulta para saber los hijos del registro a modificar
            $_POST['id_padre'] = $id;
            $_POST['caso'] = 'hijosRecuperado';

            foreach ($hijos->aaData as $hijo) { // Recorre el resto de hijos y cambia el id_padre
              $_POST['id'] = $hijo[0];
              $retorno = $objArtista->update($_POST);
            }

            $_POST['id'] = $padre;
            $_POST['caso'] = 'recuperado';
            $retorno = $objArtista->update($_POST); // Ponemos al padre anterior el id de actual como padre.
        } else { // no es histórico
            $idNuevo = $objArtista->addEmptyRow('artista');  // Crea registro vacíoSession::unsetVariable('username');
            $_POST['id'] = $idNuevo;
            $_POST['id_padre'] = 0;
            $_POST['caso'] = 'nuevo';
            $retorno = $objArtista->update($_POST); // mete los datos a un registro nuevo

            if (is_numeric($id)) {
                $_POST['id'] = $id;
                $_POST['id_padre'] = $idNuevo;
                $_POST['caso'] = 'anterior';
                $retorno = $objArtista->update($_POST); // Al anterior le pone como padre el nuevo registro
                $peticion_hijos = [
                  'tabla' => 'artista',
                  'columnas' => 'id',
                  'conditions' => "id_padre=$id"
                ];
                $hijos = json_decode($objArtista->getData($peticion_hijos));  // Consulta para saber los hijos del registro a modificar
                $_POST['id_padre'] = $idNuevo;
                $_POST['caso'] = 'hijos';

                foreach ($hijos->aaData as $hijo) { // Recorre el resto de hijos y cambia el id_padre
                  $_POST['id'] = $hijo[0];
                  $retorno = $objArtista->update($_POST); // A los hijos anteriores les pone como padre el nuevo registro
                }
            }
        }

        if ($retorno) {
            $logo = $objArtista->getImageName('artista', $id, 0, true);

            if (isset($_POST['deleteLogo'])&&$_POST['deleteLogo']==1) {
                if (file_exists(IMAGE_PATH . $logo) && $logo!='') {
                    unlink(IMAGE_PATH . $logo);
                }

                $objArtista->updateImageFile('artista','logo', $id, '');
            }

            if (isset($_FILES['logo'])&&$_FILES['logo']['name']!='') {
                if (file_exists(IMAGE_PATH . $logo) && $logo!='') {
                    unlink(IMAGE_PATH . $logo);
                }

                $origin = $_FILES['logo']['tmp_name'];
                $destination = $this->makeName($_FILES['logo']['name']);

                if ($this->saveFileToDisk($origin, $destination)) {
                    $objArtista->updateImageFile('artista','logo', $id,
                            $destination);
                }
            }

            $images = $objArtista->getImageName('artista', $id, $this->getNumImages());

            for ($i = 1; $i <= $this->getNumImages(); $i++) {
                $image = $images[$i-1];

                if (isset($_POST["deleteImage$i"]) && $_POST["deleteImage$i"] == 1) {
                    if(file_exists(IMAGE_PATH . $image) && $image!='') unlink(IMAGE_PATH . $image);
                    $objArtista->updateImageFile('artista','imagen'.$i, $id, '');
                }

                if (isset($_FILES['imagen'.$i])&&$_FILES['imagen'.$i]['name']!='') {
                    if(file_exists(IMAGE_PATH . $image) && $image!='' && strpos($image, 'ipsum')===FALSE) unlink(IMAGE_PATH . $image);
                    $origin = $_FILES['imagen'.$i]['tmp_name'];
                    $destination = $this->makeName($_FILES['imagen'.$i]['name']);
                    if($this->saveFileToDisk($origin, $destination))
                      $objArtista->updateImageFile('artista','imagen'.$i, $id, $destination);
                }
            }
            $files = $objArtista->getFileName('artista', $id, $this->getNumFiles());
            for($i = 1; $i <= $this->getNumFiles(); $i++) {
                $file = $files[$i-1];
                if($_POST['deleteFichero'.$i]==1) {
                    if(file_exists(IMAGE_PATH . $file) && $file!='') unlink(IMAGE_PATH . $file);
                    $objArtista->updateImageFile('artista','fichero'.$i, $id, '');
                }
                if(isset($_FILES['fichero'.$i]['name'])&&$_FILES['fichero'.$i]['name']!='') {
                    if(file_exists(IMAGE_PATH . $file) && $file!='') unlink(IMAGE_PATH . $file);
                    $origin = $_FILES['fichero'.$i]['tmp_name'];
                    $destination = $this->makeName($_FILES['fichero'.$i]['name']);
                    if(move_uploaded_file($origin, IMAGE_PATH.$destination))
                      $objArtista->updateImageFile('artista','fichero'.$i, $id, $destination);
                }
            }
        }
        $this->redirect('index.php?c=Artista&m=grid');
    }
    public function delete()
    {
        $objArtista = new ArtistaModel();
        $id = $_REQUEST['id'];
        if ($id) {
            if ($_REQUEST['id_padre'] == 0) {
                $ultimo_hijo = [
                  'tabla' => 'artista',
                  'columnas' => 'id',
                  'conditions' => "id_padre=$id",
                  'iSortCol_0' => 'fecha_modificacion',
                  'iDisplayStart' => 0,
                  'iDisplayLength' => 1
                ];
                $hijo = json_decode($objArtista->getData($ultimo_hijo));  // Consulta para saber los hijos del registro a borrar

                $_POST = [
                  'id' => $hijo->aaData[0][0],
                  'id_padre' => 0
                ];
                $retorno = $objArtista->update($_POST);
            }


            $images = $objArtista->getImageName('artista', $id, $this->getNumImages());
            if ($images) {
                foreach($images as $image) {
                    if(file_exists(IMAGE_PATH . $image) && $image != '')
                      unlink(IMAGE_PATH . $image);
                }
            }
            $files = $objArtista->getFileName('artista', $id, $this->getNumFiles());
            if ($files) {
                foreach($files as $file) {
                    if(file_exists(IMAGE_PATH . $file) && $file != '')
                      unlink(IMAGE_PATH . $file);
                }
            }
            $objArtista->delete($id);
        }
        $this->redirect('index.php?c=Artista&m=grid');
    }
    public function add()
    {
        $this->setView('artistaView.tpl.php');
        $this->setJs('artistaView.js');
        $emptyObject = (object) array(
          'id'=>'',
          'fecha_insercion'=>'',
          'nombre'=>'',
          'contactos'=>'',
          'conexiones'=>'',
          'notas'=>'',
          'logotipo'=>'',
          'visible'=>'1',
          'orden'=>'',
          'id_usuario'=>'',
          'fecha_modificacion'=>'',
          'id_padre'=>'',
      );
        return $emptyObject;
    }
}
