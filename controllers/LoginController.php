<?php
require_once 'lib/Controller.php';
class LoginController extends Controller
{
	public function __construct()
	{
		$this->setNumImages(0);
		$this->setNumFiles(0);
	}
	public function show()
	{
		$this->setView('login.php');
		$this->setJs('login.js');
	}
	public function getData()
	{
		$objUsuario = new UsuarioModel();
        $params = $_REQUEST;
        $isAdmin = $this->user()->is_admin;
        if (! $isAdmin)
        {
            $id = $this->user()->id;

           $params['conditions'] = array_key_exists('conditions', $params) && $params['conditions'] != "1" ? " AND id = '$id'" : "id = '$id'";
        }
        $data = $objUsuario->getData($params);

		$this->setJs('usuario.js');
		echo $data;
	}
	public function edit()
	{
		$id = $_REQUEST['id'];
		if (! $this->user()->is_admin && $id != $this->user()->id)
        {
            $this->redirect('main.php?c=Usuario&m=grid'); die();
        }

		$objUsuario = new UsuarioModel();
		$row = $objUsuario->getOne($id);
		if($row)
		{
			$this->setView('usuarioView.tpl.php');
			$this->setJs('usuarioView.js');

			return $row;
		}
		else
		{
			$this->redirect('main.php?c=Usuario&m=grid'); die();
		}

	}
	public function update()
	{
	    if(!isset($_POST['id'])) {$this->redirect('main.php?c=Usuario&m=grid');die();}

        if (! $this->user()->is_admin && $_POST['id'] != $this->user()->id)
        {
            $this->redirect('main.php?c=Usuario&m=grid'); die();
        }

		$objUsuario = new UsuarioModel();
	  $objUsuario->startTransaction();
		$id = $_POST['id'];
    $_POST['id_usuario'] = Session::getVariable('user_id');
		$isNew = false;
		if(!is_numeric($id)) {
		    $isNew = true;
			  $id = $objUsuario->addEmptyRow('usuario', ['created_at' => date('Y-m-d H:i:s')]);
			  $_POST['id'] = $id;
		}

		try
        {
            $retorno = $objUsuario->update($_POST);
            $objUsuario->commitTransaction();
            $this->redirect('main.php?c=Usuario&m=edit&id='.$_POST['id']);
        }
        catch(PasswordsNotEquals $e)
        {
            $objUsuario->rollbackTransaction();
            if ($isNew)
            {
                $this->redirect('main.php?c=Usuario&m=add&pne=1');
            }
            else
            {
                $this->redirect('main.php?c=Usuario&m=edit&id='.$_POST['id'].'&pne=1');
            }

        }/*
        catch(NickIsRequired $e)
        {
            $objUsuario->rollbackTransaction();
            if ($isNew)
            {
                $this->redirect('main.php?c=Usuario&m=add&nr=1');
            }
            else
            {
                $this->redirect('main.php?c=Usuario&m=edit&id='.$_POST['id'].'&nr=1');
            }
        }*/

        catch(EmailIsRequired $e)
        {
            $objUsuario->rollbackTransaction();
            if ($isNew)
            {
                $this->redirect('main.php?c=Usuario&m=add&er=1');
            }
            else
            {
                $this->redirect('main.php?c=Usuario&m=edit&id='.$_POST['id'].'&er=1');
            }
        }
	}
	public function delete()
	{
		$id = $_REQUEST['id'];
		if($id)
		{

            if (! $this->user()->is_admin && $id != $this->user()->id)
            {
                $this->redirect('main.php?c=Usuario&m=grid'); die();
            }

			$objClientes = new UsuarioModel();
			$images = $objClientes->getImageName('usuario', $id, $this->getNumImages());
			if($images){
				foreach($images as $image)
				{
					if(file_exists(IMAGE_PATH . $image) && $image!='')
						unlink(IMAGE_PATH . $image);
				}
			}
			$files = $objClientes->getFileName('clientes', $id, $this->getNumFiles());
			if($files){
				foreach($files as $file)
				{
					if(file_exists(IMAGE_PATH . $file) && $file!='')
						unlink(IMAGE_PATH . $file);
				}
			}
			$objClientes->delete($id);
		}
		$this->redirect('main.php?c=Usuario&m=grid');
	}
	public function add()
	{

        if (! $this->user()->is_admin)
        {
            $this->redirect('main.php?c=Usuario&m=grid'); die();
        }
		$this->setView('usuarioView.tpl.php');
		$this->setJs('usuarioView.js');
		$emptyObject = (object) array('id'=>'','fecha_insercion'=>'','nombre'=>'','contactos'=>'','conexiones'=>'','notas'=>'','logotipo'=>'','visible'=>'1','orden'=>'',
		);
		return $emptyObject;
	}

}
