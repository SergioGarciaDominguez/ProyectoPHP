<?php
  //ob_start();

    ini_set('display_errors', true);
    error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

	//codificación
	header ('Content-type: text/html; charset=utf-8');


	//include_once 'vendor/autoload.php';
  require_once 'config.php';
  require_once 'controllers/UserController.php';
  require_once 'lib/Session.php';
	//sesión
	Session::create(ADMIN_SESSION_ID);

	//autenticacion del administrador
	if(isset($_POST['username']) && isset($_POST['password']))
	{
		$user = new UserController;
		if(!$user->autentication($_POST))
			echo "no";
		else
			echo "ok";
		$content = ob_get_contents();
		ob_get_clean();
		die($content);
	}

	//autenticacion del administrador por COOKIE
	if(isset($_COOKIE['username']) && isset($_COOKIE['password']) && Session::getVariable('autentication')!= sha1(Session::getVariable('username')))
	{
		$user = new UserController;
		$user->autentication($_COOKIE);
	}

	//variables get controlador y método
	if (!isset($_GET['c']))
		$getController = $getModel = "cancion";
	else
		$getController = $getModel = $_GET['c'];
	if (!isset($_GET['c']))
		$getMethod = "grid";
	else
		$getMethod = $_GET['m'];


	//modelo
	$model = ucfirst($getModel) . 'Model';
	$pathModel = 'models/' . $model . '.php';
	if(file_exists($pathModel))
		include($pathModel);


	//controlador
	$controller = ucfirst($getController) . 'Controller';
	$pathController = 'controllers/' . $controller . '.php';
	if(!file_exists($pathController))
		die('No se ha podido cargar el controlador.<br />');
	else
		include($pathController);


	//creación del objeto controlador
	$obj = new $controller;
	//$obj->setUser($_SESSION['user_id']);


	//metodo del controlador a ejecutar
	if(!method_exists($obj, $getMethod))
		die('No existe el método ' . $getMethod . '<br />');
	else {
		    if (($getController=="aptitudesLogos")&&(($getMethod=="edit")||($getMethod=="add"))){
				$objAptitudesCategorias = new AptitudesCategoriasModel();
				$categorias = $objAptitudesCategorias->getAll();
        	}
		$result = $obj->$getMethod();
		}

	//template principal
	if($obj->getView())
		include("main.php");
