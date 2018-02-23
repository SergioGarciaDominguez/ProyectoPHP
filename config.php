<?php

	/*echo "EL PANEL HA SIDO MIGRADO. CONTACTE CON EL ADMINISTRADOR DEL SISTEMA.";
 	exit;*/

	//nombre proyecto
	define('PROJECT_NAME','MVC');
	//url base
	//if($_SERVER['HTTP_HOST']=='localhost'){
		define('BASE_URL', 'http://' .  $_SERVER['HTTP_HOST'] . '/skynet/');
		define('BASE_IMAGE_URL', 'http://' .  $_SERVER['HTTP_HOST'] . '/skynet/');
		define('IMAGE_PATH', 'http://' .  $_SERVER['HTTP_HOST'] . '/skynet/res/uploads/');
	/*}
	else if($_SERVER['HTTP_HOST']=='192.168.1.175'){
		define('BASE_URL', 'http://' .  $_SERVER['HTTP_HOST'] . '/dropbox/Panel/');
		define('BASE_IMAGE_URL', 'http://' .  $_SERVER['HTTP_HOST'] . '/Panel/');
		define('IMAGE_PATH', 'http://' .  $_SERVER['HTTP_HOST'] . '/Panel/res/uploads/');
	}
	else if($_SERVER['HTTP_HOST']=='panel.solbyte.com.es'){
		define('BASE_URL', 'https://' .  $_SERVER['HTTP_HOST'] . '/');
		define('BASE_IMAGE_URL', 'https://' .  $_SERVER['HTTP_HOST'] . '/');
		define('IMAGE_PATH', 'https://' .  $_SERVER['HTTP_HOST'] . '/res/uploads/');
	}
	else die('Falta BASE_URL definir en config/config.php');
	*/
	//id de sesión
	define('ADMIN_SESSION_ID','jgk2l3tdGslatrans2dl');

	define('KEY','crptPanelSolbyte2014');

	$idiomas=array();
	$idiomas[]=array('Inglés','ingles','en');
	$idiomas[]=array('Francés','frances','fr');
	$idiomas[]=array('Alemán','aleman','de');
	$idiomas[]=array('Portugués','portugues','pt');
	$idiomas[]=array('Italiano','italiano','it');
	$idiomas[]=array('Ruso','ruso','ru');
	$idiomas[]=array('Chino','chino','ch');
	$idiomas[]=array('Árabe','arabe','ar');
	//$idiomas[]=array('Polaco','polaco','po');
	//$idiomas[]=array('Húngaro','hungaro','hu');
	$GLOBALS['idiomas']=$idiomas;

	//conexion bd

	/*define('MYSQL_HOST', 'servidor');
	define('MYSQL_USER', 'crm');
	define('MYSQL_PASSWORD', 'crm');
	define('MYSQL_DATABASE', 'panel');
	define('MYSQL_ENCODING', 'utf8');
	define('MYSQL_HOST', 'localhost');
	define('MYSQL_USER', 'panel');
	define('MYSQL_PASSWORD', 'Panelaco@2014#');*/

	define('MYSQL_HOST', 'localhost');
	define('MYSQL_USER', 'root');
	define('MYSQL_PASSWORD', '');
	define('MYSQL_DATABASE', 'proyecto_ajax');
	define('MYSQL_ENCODING', 'utf8');

	define('SMTP_HOST', '');
    define('SMTP_HOST', '');
