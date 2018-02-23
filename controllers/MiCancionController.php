<?php
require_once 'lib/Controller.php';
require_once 'models/MiCancionModel.php';
class MiCancionController extends Controller
{
	public function __construct()
	{
		$this->setNumImages(0);
		$this->setNumFiles(0);
	}
	public function grid()
	{
    $objCancion = new MiCancionModel();
    $request['columnas'] = '*';
    $usuario = $this->user()->id;
    $request['conditions'] = "$usuario = (SELECT usuario_id FROM usuario_cancion INNER JOIN cancion ON cancion_mbid = mbid)";
    $data = $objCancion->getData($request);
		$this->setView('views/miCancion.php');
	}
	public function getData()
	{
		$objMiCancion = new MiCancionModel();
		$data = $objMiCancion->getData($_REQUEST);
		$this->setJs('miCancion.js');
		echo $data;
	}
	public function edit()
	{
		$id = $_REQUEST['id'];
		$objMiCancion = new MiCancionModel();
		$row = $objMiCancion->getOne($id);
		if($row)
		{
			$this->setView('miCancionView.tpl.php');
			$this->setJs('miCancionView.js');

			return $row;
		}
		else
		{
			$this->redirect('main.php?c=MiCancion&m=grid'); die();
		}

	}
	public function update()
	{
		if(!isset($_POST['id'])) {$this->redirect('main.php?c=MiCancion&m=grid');die();}
		$objMiCancion = new MiCancionModel();
		$id = $_POST['id'];
    $_POST['id_usuario'] = Session::getVariable('user_id');
		if(!is_numeric($id))
		{
			$id = $objMiCancion->addEmptyRow('mi_cancion');
			$_POST['id'] = $id;
		}

		$retorno = $objMiCancion->update($_POST);
		if($retorno)
		{
			$logo = $objMiCancion->getImageName('mi_cancion', $id, 0, true);
			if(isset($_POST['deleteLogo'])&&$_POST['deleteLogo']==1)
			{
				if(file_exists(IMAGE_mi_cancionPATH . $logo) && $logo!='') unlink(IMAGE_PATH . $logo);
				$objMiCancion->updateImageFile('mi_cancion','logo', $id, '');
			}
			if(isset($_FILES['logo'])&&$_FILES['logo']['name']!='')
				{
				if(file_exists(IMAGE_PATH . $logo) && $logo!='') unlink(IMAGE_PATH . $logo);
				$origin = $_FILES['logo']['tmp_name'];
				$destination = $this->makeName($_FILES['logo']['name']);
				if($this->saveFileToDisk($origin, $destination))
					$objMiCancion->updateImageFile('mi_cancion','logo', $id, $destination);
			}
			$images = $objMiCancion->getImageName('mi_cancion', $id, $this->getNumImages());
			for($i=1; $i<=$this->getNumImages(); $i++)
			{
				$image = $images[$i-1];
				if(isset($_POST['deleteImage'.$i])&&$_POST['deleteImage'.$i]==1)
				{
					if(file_exists(IMAGE_PATH . $image) && $image!='') unlink(IMAGE_PATH . $image);
					$objMiCancion->updateImageFile('mi_cancion','imagen'.$i, $id, '');
				}
				if(isset($_FILES['imagen'.$i])&&$_FILES['imagen'.$i]['name']!='')
				{
					if(file_exists(IMAGE_PATH . $image) && $image!='' && strpos($image, 'ipsum')===FALSE) unlink(IMAGE_PATH . $image);
					$origin = $_FILES['imagen'.$i]['tmp_name'];
					$destination = $this->makeName($_FILES['imagen'.$i]['name']);
					if($this->saveFileToDisk($origin, $destination))
						$objMiCancion->updateImageFile('mi_cancion','imagen'.$i, $id, $destination);
				}
			}
			$files = $objMiCancion->getFileName('mi_cancion', $id, $this->getNumFiles());
			for($i=1; $i<=$this->getNumFiles(); $i++)
			{
				$file = $files[$i-1];
				if($_POST['deleteFichero'.$i]==1)
				{
					if(file_exists(IMAGE_PATH . $file) && $file!='') unlink(IMAGE_PATH . $file);
					$objMiCancion->updateImageFile('mi_cancion','fichero'.$i, $id, '');
				}
				if(isset($_FILES['fichero'.$i]['name'])&&$_FILES['fichero'.$i]['name']!='')
				{
					if(file_exists(IMAGE_PATH . $file) && $file!='') unlink(IMAGE_PATH . $file);
					$origin = $_FILES['fichero'.$i]['tmp_name'];
					$destination = $this->makeName($_FILES['fichero'.$i]['name']);
					if(move_uploaded_file($origin, IMAGE_PATH.$destination))
						$objMiCancion->updateImageFile('mi_cancion','fichero'.$i, $id, $destination);
				}
			}
		}
		$this->redirect('main.php?c=MiCancion&m=edit&id='.$_POST['id']);
	}
	public function delete()
	{
		$id = $_REQUEST['id'];
		if($id)
		{
			$objMiCancion = new MiCancionModel();
			$images = $objMiCancion->getImageName('mi_cancion', $id, $this->getNumImages());
			if($images){
				foreach($images as $image)
				{
					if(file_exists(IMAGE_PATH . $image) && $image!='')
						unlink(IMAGE_PATH . $image);
				}
			}
			$files = $objMiCancion->getFileName('mi_cancion', $id, $this->getNumFiles());
			if($files){
				foreach($files as $file)
				{
					if(file_exists(IMAGE_PATH . $file) && $file!='')
						unlink(IMAGE_PATH . $file);
				}
			}
			$objMiCancion->delete($id);
		}
		$this->redirect('main.php?c=MiCancion&m=grid');
	}
	public function add()
	{
		$this->setView('miCancionView.tpl.php');
		$this->setJs('miCancionView.js');
		$emptyObject = (object) array('id'=>'','fecha_insercion'=>'','nombre'=>'','descripcion'=>'',
		);
		return $emptyObject;
	}
}
