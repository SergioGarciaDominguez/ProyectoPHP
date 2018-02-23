<?php
require_once 'lib/Controller.php';

class UserController extends Controller
{

	public function __construct() {}


	public function autentication ($login)
	{
		$objLogin = new UserModel;

		$user = $objLogin->userLogin($login['username'], $login['password']);

		if(!$user)
			return false;
		else
		{
		    Session::setVariable('username',$user->nick);
            Session::setVariable('user_id',$user->id);
            Session::setVariable('password',sha1($login['password']));
            Session::setVariable('autentication',sha1($user->nick));
			return true;
		}

	}

	public function close ()
	{
		Session::unsetVariable('autentication');
		Session::unsetVariable('password');
        Session::unsetVariable('username');
        Session::unsetVariable('user_id');
		Session::destroy();
		$this->redirect('index.php');
	}

	public function password ()
	{
	    die();
		$this->setView('password.tpl.php');
		$this->setJs('user.js');
	}


}
